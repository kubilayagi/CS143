from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import udf
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
	labeled_comments = context.sql("select comments.id, labeled_data.labeldjt, body, author, author_flair_text, link_id, score, created_utc from labeled_data inner join comments on comments.id = labeled_data.Input_id")
	#labeled_comments.select("id", "Input_id").show()
	labeled_comments.show()




	# TASK 4
	# Code for task 4...
	context.udf.register("sanitize", lambda body: reduce(lambda acc, elem: acc + elem.split(), sanitize(body)[1:], []), ArrayType(StringType()))
	labeled_comments.createOrReplaceTempView("labeled_comments")
	combined = context.sql("select *, sanitize(body) as words from labeled_comments")

	combined.printSchema()
	combined.select("body", "words").show()


	# TASK 6A
	# Code for task 6A...
	cv = CountVectorizer(inputCol="words", outputCol="features", minDF=5.0, binary=True)
	model = cv.fit(combined)
	vectorized = model.transform(combined)
    
    
    
    #TASK 6B
    # Code for task 6B
	vectorized.createOrReplaceTempView("vectorized")
	labeled = context.sql("select *, case when labeldjt = 1 then 1 else 0 end as posLabel, case when labeldjt = -1 then 1 else 0 end as negLabel from vectorized")
	labeled.show()






if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)










