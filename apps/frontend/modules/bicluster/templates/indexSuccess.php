<h2>Mining bi clusters List</h2>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Mining</th>
      <th>Config</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($mining_bi_clusters as $mining_bi_cluster): ?>
    <tr>
      <td><a href="<?php echo url_for('bicluster/show?id='.$mining_bi_cluster->getId()) ?>"><?php echo $mining_bi_cluster->getId() ?></a></td>
      <td><?php echo $mining_bi_cluster->getMiningId() ?></td>
      <td><?php echo $mining_bi_cluster->getConfigId() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>