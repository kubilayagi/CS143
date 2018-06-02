from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
import csv
import os

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
    
    if(os.path.exists(comments_parquet)):
    	comments_DF = sqlContext.read.parquet(comments_parquet)
    else:
    	comments_DF = sqlContext.read.json("comments-minimal.json.bz2")
    	comments_DF.write.parquet(comments_parquet)

    if(os.path.exists(submissions_parquet)):
    	submissions_DF = sqlContext.read.parquet(submissions_parquet)
    else:
    	submissions_DF = sqlContext.read.json("submissions.json.bz2")
    	submissions_DF.write.parquet(submissions_parquet)

    if(os.path.exists(labeled_data_parquet)):
    	labeled_data_DF = sqlContext.read.parquet(labeled_data_parquet)
    else:
    	labeled_data_DF = sqlContext.read.csv("labeled_data.csv")
    	labeled_data_DF.write.parquet(labeled_data_parquet)

    # TASK 2
	# Code for task 2...
	labeled_data_DF.createOrReplaceTempView("labeled_data")
	comments_DF.createOrReplaceTempView("comments")
	labeled_comments = context.sql("select * from labeled_data inner join comments on comment_id")

if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)

