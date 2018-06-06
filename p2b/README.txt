Kubilay Agi
Lawrence Chen

Questions:
1.) There are 7 functional dependencies implied by this data:
Input_id -> {labeldjt, labeldem, labelgop}
Input_id -> {labeldjt, labeldem}
Input_id -> {labeldjt, labelgop}
Input_id -> {labeldem, labelgop}
Input_id -> labeldjt
Input_id -> labeldem
Input_id -> labelgop

2.) The comments table is not normalized. We notice that there are attributes for subreddit_id and subreddit (which we interpreted to be the name of the subreddit). This can be decomposed into a single separate table because subreddits can be identified by their id, there is no need to add the name as well. 

Additionally, assuming that the author_flair_text does not change depending on the location of where the author is and that a cakeday means a birthday, we do not need to include author_flair_text or the author_cakeday in this table. We can have a separate table that associates the author of a comment with their flair text and their birthday. This data is redundant.

Perhaps they chose to store the data in this way to be able to display the path on the comment using the subreddit name, instead of having to look it up for every comment. They might have included the flair text and cakeday attributes in the table as well to make styling faster (i.e. displaying each users' flair text quickly, instead of doing a lookup for every author that posted something in the comments).

3.) 
The join query to be explained (an inner join):

select s.id as submission_id, s.created_utc, s.author_flair_text, c.body as body, c.id as comment_id from comments c inner join submissions s on s.id = SUBSTR(c.link_id, 4, LENGTH(c.link_id) - 3)


Explain output:

== Physical Plan ==
*(2) Project [id#27 AS submission_id#415, created_utc#17L, author_flair_text#12, body#132, id#142 AS comment_id#417]
+- *(2) BroadcastHashJoin [substring(link_id#144, 4, (length(link_id#144) - 3))], [id#27], Inner, BuildRight
   :- *(2) Project [body#132, id#142, link_id#144]
   :  +- *(2) Filter isnotnull(link_id#144)
   :     +- *(2) FileScan parquet [body#132,id#142,link_id#144] Batched: true, Format: Parquet, Location: InMemoryFileIndex[file:/media/sf_vm-shared/comments-minimal.parquet], PartitionFilters: [], PushedFilters: [IsNotNull(link_id)], ReadSchema: struct<body:string,id:string,link_id:string>
   +- BroadcastExchange HashedRelationBroadcastMode(List(input[2, string, true]))
      +- *(1) Project [author_flair_text#12, created_utc#17L, id#27]
         +- *(1) Filter isnotnull(id#27)
            +- *(1) FileScan parquet [author_flair_text#12,created_utc#17L,id#27] Batched: true, Format: Parquet, Location: InMemoryFileIndex[file:/media/sf_vm-shared/submissions.parquet], PartitionFilters: [], PushedFilters: [IsNotNull(id)], ReadSchema: struct<author_flair_text:string,created_utc:bigint,id:string>

We can see from the output that Spark is using a Hash join for this inner join. It builds the hash table on the join keys that were used for the inner join. This is used when one of the dataframes is small enough to fit in the memory of a single machine. It then broadcasts the dataframe to all other cores that each have a chunk of the larger dataframe. The join is then computed at each of the cores. 
