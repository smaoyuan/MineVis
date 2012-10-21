#
# The point of this query is to generate a unique set of tables from
# project_configs for a set project made of table A and B
#

#get distinct list from the first column
SELECT DISTINCT p1.table_a AS chain_type FROM `project_config` p1 WHERE p1.project_id=1;

#same for the second one
SELECT DISTINCT p2.table_b AS chain_type FROM `project_config` p2 WHERE p2.project_id=1;


#union the 2!
SELECT DISTINCT table_a AS chain_type FROM `project_config` WHERE project_id=1
UNION
SELECT DISTINCT table_b AS chain_type FROM `project_config` WHERE project_id=1;


#now get the clusters select mining and type
SELECT * FROM mining_bi_cluster WHERE mining_id=1;

SELECT bicluster.id, config.table_a, config.table_b FROM mining_bi_cluster bicluster
INNER JOIN project_config config
ON bicluster.config_id = config.id
WHERE bicluster.mining_id=1;

#do it all for a select mining and type

SELECT bicluster.id, config.table_a, config.table_b FROM mining_bi_cluster bicluster
INNER JOIN project_config config
ON bicluster.config_id = config.id
WHERE bicluster.mining_id=1
AND (
    config.table_a = 'planets'
    OR
    config.table_b = 'planets'
)