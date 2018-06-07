import matplotlib
matplotlib.use('Agg') # Must be before importing matplotlib.pyplot or pylab!
import matplotlib.pyplot as plt
from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import udf
from pyspark.sql.functions import from_unixtime
import csv
import os
from cleantext import sanitize
from pyspark.sql.types import ArrayType
from pyspark.sql.types import StringType
from functools import reduce

# IMPORT OTHER MODULES HERE

def main(context):
	"""Main function takes a Spark SQL context."""
	
	# TASK 1
	# Code for task 1...
	comments_DF = None
	submissions_DF = None
	labeled_data_DF = None

	comments_parquet = os.path.abspath("./comments-minimal.parquet")
	submissions_parquet = os.path.abspath("./submissions.parquet")
	labeled_data_parquet = os.path.abspath("./labeled_data.parquet")

	if(os.path.exists(labeled_data_parquet)):
		labeled_data_DF = context.read.parquet(labeled_data_parquet)
	else:
		labeled_data_DF = context.read.csv("labeled_data.csv", header=True)
		labeled_data_DF.write.parquet(labeled_data_parquet)

	if(os.path.exists(submissions_parquet)):
		submissions_DF = context.read.parquet(submissions_parquet)
	else:
		submissions_DF = context.read.json("submissions.json.bz2")
		submissions_DF.write.parquet(submissions_parquet)


	if(os.path.exists(comments_parquet)):
		comments_DF = context.read.parquet(comments_parquet)
	else:
		comments_DF = context.read.json("comments-minimal.json.bz2")
		comments_DF.write.parquet(comments_parquet)
	
	
	# TASK 2
	# Code for task 2...
	comments_DF.printSchema()
	print("************")
	submissions_DF.printSchema()
	print("************")
	labeled_data_DF.printSchema()
	print("************")

	labeled_data_DF.createOrReplaceTempView("labeled_data")
	comments_DF.createOrReplaceTempView("comments")
	labeled_comments = context.sql("select comments.id, cast(labeled_data.labeldjt as int) as label, body, author, author_flair_text, link_id, score, created_utc from labeled_data inner join comments on comments.id = labeled_data.Input_id")
	#labeled_comments.select("id", "Input_id").show()
	labeled_comments.show()




	# TASK 4, 5
	# Code for tasks 4 and 5
	context.udf.register("sanitize", lambda body: reduce(lambda acc, elem: acc + elem.split(), sanitize(body)[1:], []), ArrayType(StringType()))
	labeled_comments.createOrReplaceTempView("labeled_comments")
	combined = context.sql("select *, sanitize(body) as words from labeled_comments")

	combined.printSchema()
	combined.select("body", "words").show()

	print("********************* TEST *************")
	# TASK 6A
	# Code for task 6A...
	cv = CountVectorizer(inputCol="words", outputCol="features", minDF=5.0, binary=True, vocabSize=1 << 18)
	print("********************* TEST *************")
	vectorize_model = cv.fit(combined)
	vectorized = vectorize_model.transform(combined)
	vectorize_model.write().overwrite().save("www/vector.model")
	print("********************* TEST *************")

	# TASK 6B
	# Code for task 6B...
	vectorized.createOrReplaceTempView("vectorized")
	labeled = context.sql("select *, case when label = 1 then 1 else 0 end as poslabel, case when label = -1 then 1 else 0 end as neglabel from vectorized")
	labeled.show()

	# TASK 7
	# Code for task 7...
	pos = labeled
	neg = labeled

	# Bunch of imports (may need more)
	from pyspark.ml.classification import LogisticRegression
	from pyspark.ml.tuning import CrossValidator, CrossValidatorModel, ParamGridBuilder
	from pyspark.ml.evaluation import BinaryClassificationEvaluator

	posmodel_path = os.path.abspath("www/pos.model")
	negmodel_path = os.path.abspath("www/neg.model")

	# Initialize two logistic regression models.
	# Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
	poslr = LogisticRegression(labelCol="poslabel", featuresCol="features", maxIter=10)
	neglr = LogisticRegression(labelCol="neglabel", featuresCol="features", maxIter=10)
	poslr.setThreshold(0.2)
	neglr.setThreshold(0.25)
	# This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
	posEvaluator = BinaryClassificationEvaluator()
	negEvaluator = BinaryClassificationEvaluator()
	# There are a few parameters associated with logistic regression. We do not know what they are a priori.
	# We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
	# We will assume the parameter is 1.0. Grid search takes forever.
	posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
	negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
	# We initialize a 5 fold cross-validation pipeline.
	posCrossval = CrossValidator(
		estimator=poslr,
		evaluator=posEvaluator,
		estimatorParamMaps=posParamGrid,
		numFolds=5)
	negCrossval = CrossValidator(
		estimator=neglr,
		evaluator=negEvaluator,
		estimatorParamMaps=negParamGrid,
		numFolds=5)
	# Although crossvalidation creates its own train/test sets for
	# tuning, we still need a labeled test set, because it is not
	# accessible from the crossvalidator (argh!)
	# Split the data 50/50
	posTrain, posTest = pos.randomSplit([0.5, 0.5])
	negTrain, negTest = neg.randomSplit([0.5, 0.5])

	print("********************* TEST *************")
	posModel = None
	negModel = None
	
	# Train the models
	if(os.path.exists(posmodel_path)):
		posModel = CrossValidatorModel.load(posmodel_path)
	else:
		print("Training positive classifier...")
		posModel = posCrossval.fit(posTrain)
		# Once we train the models, we don't want to do it again. We can save the models and load them again later.
		posModel.write().overwrite().save(posmodel_path)
	
	if(os.path.exists(negmodel_path)):
		negModel = CrossValidatorModel.load(negmodel_path)
	else:
		print("Training negative classifier...")
		negModel = negCrossval.fit(negTrain)
		# Once we train the models, we don't want to do it again. We can save the models and load them again later.
		negModel.write().overwrite().save(negmodel_path)


	# TEST MODEL
	posResult = posModel.transform(posTest)
	posResult.createOrReplaceTempView("posResult")
	posAccuracy = context.sql("select avg(case when poslabel = prediction then 1 else 0 end) as accuracy from posResult")
	posAccuracy.show()

	negResult = negModel.transform(negTest)
	negResult.createOrReplaceTempView("negResult")
	negAccuracy = context.sql("select avg(case when neglabel = prediction then 1 else 0 end) as accuracy from negResult")
	negAccuracy.show()

	# PLOT ROC CURVE
	from pyspark.mllib.evaluation import BinaryClassificationMetrics as metric

	results = posResult.select(['probability', 'poslabel'])
	results_collect = results.collect()
	results_list = [(float(i[0][0]), 1.0-float(i[1])) for i in results_collect]
	scoreAndLabels = sc.parallelize(results_list) 
	metrics = metric(scoreAndLabels)

	from sklearn.metrics import roc_curve, auc
	fpr = dict()
	tpr = dict()
	roc_auc = dict() 
	y_test = [i[1] for i in results_list]
	y_score = [i[0] for i in results_list]
	fpr, tpr, _ = roc_curve(y_test, y_score)
	roc_auc = auc(fpr, tpr)

	plt.figure()
	plt.plot(fpr, tpr, 'g--', label='Trump Positive Sentiment, ROC curve (area = %0.2f)' % roc_auc)
	plt.plot([0, 1], [0, 1], 'k--')
	plt.xlim([0.0, 1.0])
	plt.ylim([0.0, 1.05])
	plt.xlabel('False Positive Rate')
	plt.ylabel('True Positive Rate')
	plt.title('Receiver operating characteristic example')
	plt.legend(loc="lower right")

	results = negResult.select(['probability', 'neglabel'])
	results_collect = results.collect()
	results_list = [(float(i[0][0]), 1.0-float(i[1])) for i in results_collect]
	scoreAndLabels = sc.parallelize(results_list)
	metrics = metric(scoreAndLabels)

	fpr = dict()
	tpr = dict()
	roc_auc = dict()
	y_test = [i[1] for i in results_list]
	y_score = [i[0] for i in results_list]
	fpr, tpr, _ = roc_curve(y_test, y_score)
	roc_auc = auc(fpr, tpr)

	plt.plot(fpr, tpr, 'r--', label='Trump Negative Sentiment, ROC curve (area = %0.2f)' % roc_auc)
	plt.legend(loc="lower right")
	plt.savefig('trump_ROC.png')

	# TASK 8
	# Code for task 8...
	#get title of submission post
	submissions_DF.createOrReplaceTempView("submissions")
	comments_DF.createOrReplaceTempView("comments")
	whole_data = context.sql("select s.id as submission_id, s.title, s.author_cakeday, s.created_utc, s.author_flair_text, s.over_18, c.controversiality, c.body as body, c.id as comment_id, c.score as comment_score, s.score as story_score from comments c inner join submissions s on s.id = SUBSTR(c.link_id, 4, LENGTH(c.link_id) - 3) where body not like '%/s' and body not like '&gt%'")
	whole_data.show(20)
	sampled = whole_data.sample(False, 0.5, 42)
	sampled.show(20)

	whole_data.count()
	sampled.count()

	# TASK 9
	# Code for task 9...
	context.udf.register("sanitize", lambda body: reduce(lambda acc, elem: acc + elem.split(), sanitize(body)[1:], []), ArrayType(StringType()))
	sampled.createOrReplaceTempView("sampled")
	combined = context.sql("select *, sanitize(body) as words from sampled")

	combined.printSchema()
	combined = combined.select("sampled.comment_id", "sampled.submission_id", "sampled.title", "sampled.created_utc", "sampled.author_flair_text", "sampled.author_cakeday", "sampled.over_18", "sampled.controversiality", "sampled.body", "words", "sampled.comment_score", "sampled.story_score")
	#combined.show()

	vectorized = vectorize_model.transform(combined)
	vectorized.show()

	posResult = posModel.transform(vectorized)
	posResult = posResult.withColumnRenamed('prediction', 'pos').drop("rawPrediction").drop("probability")
	result = negModel.transform(posResult)
	result = result.withColumnRenamed('prediction', 'neg').drop("rawPrediction").drop("probability")

	temp = result
	temp = temp.drop("body", "words", "features")
	result = result.drop("body", "words", "features", "title")
	result.show()

	# TASK 10
	# Code for task 10...
	result.createOrReplaceTempView("result")

	#number 1
	totalrows = result.count()
	PosPercentage = result.filter(result.pos == 1.0).count() * 100 / totalrows
	NegPercentage = result.filter(result.neg == 1.0).count() * 100 / totalrows

	print("Positive Percentage: {}%".format(PosPercentage))
	print("Negative Percentage: {}%".format(NegPercentage))

	# PosPercentage.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("pospercentage.csv")
	# NegPercentage.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("negpercentage.csv")

	#number 2
	#https://medium.com/@mrpowers/working-with-dates-and-times-in-spark-491a9747a1d2
	with_time = result.withColumn("date", F.from_unixtime(functions.col('created_utc')).cast(DateType()))
	with_time_pos = with_time.groupBy("date").agg(functions.sum(result.pos) / functions.count(result.pos))
	with_time_neg = with_time.groupBy("date").agg(functions.sum(result.neg) / functions.count(result.neg))
	time_data = with_time_pos.join(with_time_neg, ["date"])

	time_data.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("time_data.csv")

	# with_time_pos.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("timepos.csv")
	# with_time_neg.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("timeneg.csv")


	#number 3
	states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', \
	'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', \
	'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', \
	'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', \
	'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming']

	statelist = context.createDataFrame(states, StringType())

	#https://stackoverflow.com/questions/40421356/how-to-do-left-outer-join-in-spark-sql
	#https://docs.databricks.com/spark/latest/faq/join-two-dataframes-duplicated-column.html
	#we found that the attribute name for statelist dataframe was "value" by using printSchema()
	new_pos_states = pos_states.join(statelist, ["author_flair_text"], "inner")



	statelist = statelist.withColumnRenamed("value", "state")
	result = result.withColumnRenamed("author_flair_text", "state")
	pos_states = result.groupBy("state").agg(functions.sum(result.pos) / functions.count(result.pos))
	new_pos_states = pos_states.join(statelist, ["state"], "inner")
	new_pos_states = new_pos_states.withColumnRenamed("(sum(pos) / count(pos))", "Positive")
	#new_pos_states = new_pos_states.withColumnRenamed("author_flair_text", "state")
	neg_states = result.groupBy("state").agg(functions.sum(result.neg) / functions.count(result.neg))
	new_neg_states = neg_states.join(statelist, ["state"], "inner")
	new_neg_states = new_neg_states.withColumnRenamed("(sum(neg) / count(neg))", "Negative")
	#new_neg_states = new_neg_states.withColumnRenamed("author_flair_text", "state")
	#tried doing left_outer initially, but not all author flair text is a state, so need to do inner instead
	
	state_data = new_pos_states.join(new_neg_states, ["state"], "inner")

	state_data.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("state_data.csv")

	# pos_states.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("statepos.csv")
	# neg_states.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("stateneg.csv")

	#number 4
	commentPos = result.groupBy("comment_score").agg(functions.sum(result.pos) / functions.count(result.pos))		#for some reason scalar values don't work???
	storyPos = result.groupBy("story_score").agg(functions.sum(result.pos) / functions.count(result.pos))
	commentNeg = result.groupBy("comment_score").agg(functions.sum(result.neg) / functions.count(result.neg))
	storyNeg = result.groupBy("story_score").agg(functions.sum(result.neg) / functions.count(result.neg))

	comment_data = commentPos.join(commentNeg, ["comment_score"])
	submission_data = storyPos.join(storyNeg, ["story_score"])

	comment_data.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("comment_data.csv")
	submission_data.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("submission_data.csv")

	# commentPos.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("commentpos.csv")
	# storyPos.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("storypos.csv")
	# commentNeg.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("commentneg.csv")
	# storyNeg.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").save("storyneg.csv")

	#http://spark.apache.org/docs/2.1.0/api/python/pyspark.sql.html
	#Final Deliverable part 4
	temp.createOrReplaceTempView("temp")
	top_pos = context.sql("select title, (sum(pos) / count(pos)) as Positive from temp group by title order by Positive desc limit 10")
	top_neg = context.sql("select title, (sum(neg) / count(neg)) as Negative from temp group by title order by Negative desc limit 10")

if __name__ == "__main__":
	conf = SparkConf().setAppName("CS143 Project 2B")
	conf = conf.setMaster("local[*]")
	sc   = SparkContext(conf=conf)
	sqlContext = SQLContext(sc)
	sc.addPyFile("cleantext.py")
	main(sqlContext)
