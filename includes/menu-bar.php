<li class="dropdown yamm">
    <a href="/products/<?php echo $catSlug; ?>/">
        <span class="nav-icon"></span>
        <?php echo $row['categoryName'];?>
    </a>
    <?php if($hasSubcategories): ?>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <?php 
            mysqli_data_seek($subSql, 0);
            while($subRow=mysqli_fetch_array($subSql)) { 
                $subSlug = $subRow['s_slug'];
            ?>
                <li>
                    <a href="/products/<?php echo $catSlug; ?>/<?php echo $subSlug; ?>/">
                        <span class="dropdown-item-icon">➡️</span>
                        <?php echo $subRow['subcategory'];?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php endif; ?>
</li>