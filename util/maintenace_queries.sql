/*
todo clear bicluster records
todo clear jobs's mapping done/start fields
*/

/**
 * Clear bicluster records
 * nice for debug gets ride of possible duplicates from running mining manually.
 */
DELETE FROM mining_bi_cluster;
/*
not needed because deleted by dependency
DELETE FROM mining_bi_cluster_col;
DELETE FROM mining_bi_cluster_row;
*/


/*
 * Clear start and end date from job mappings
 * Recomended after clearing bicluster records to allow jobs to run again.
 */
 UPDATE mining_job_mapping SET start_time=null, end_time=null;
/**
 * Clears mining as well so jobs can be ran from the website too.
 */
 UPDATE mining SET complete=0, started=0;



