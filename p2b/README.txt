Kubilay Agi
Lawrence Chen

The extra credit we did was implementing our own ROC curve for our classifier model. It showed an AUC of 0.90 for the positive model, and an AUC 0.93 for the negative model.

So to make the runtime for reasonable for processing, we sampled about 50% the whole data set of comment_minimal for tasks 8 through 10. 

*** NOTE: When running our entire file with spark-submit, sometimes we get the Java insufficient memory error, unless no other program is running and the VM is solely dedicated to running the spark job of reddit_model.py. Using iPython shell and copy and pasting the code as needed allows it run with no problem at all.

For the larger bz2 files and some of the trained models such as our classifiers and vectorizers, we saved them into parquet files after first loading / creating them. This was to make the process faster and not have to reload / retrain the models. In our model, we wrote code to check if the parquet files exist, and if not, they will proceed to process the model the original way. Otherwise, if the parquet file can be found, it will simply load and use those instead. 
