Location:
<ul>
<?php foreach ($crumbs as $link) : ?>
    <li><?php echo link_to($link[1], $link[0]);?></li>
<?php endforeach; ?>
</ul>