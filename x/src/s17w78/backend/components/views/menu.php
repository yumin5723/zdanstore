<div class="nav">
	<ul class="menu clearfix">
    	<li><a href="/brands">BRANDS</a>
            <!-- brands menu -->
            <div class="navbox">
                <div class="subnav">BRANDS</div>
                <dl class="nav_brands clearfix">
                    <?php foreach ($brands as $brand):?>
                        <dd>
                            <a href="/brands/view/id/<?php echo $brand->id ?>" title=""><img src="<?php echo $brand->image ?>" width="40" height="40" /></a>
                            <span><a href="/brands/view/id/<?php echo $brand->id ?>" title=""><?php echo $brand->name ?></a></span>
                        </dd>
                    <?php endforeach ?>
                </dl>
                <div class="more"><a href="/brand" title="">MORE&gt;</a></div>
            </div>
        </li>
        <li><a href="#">MANS</a>
            <!-- mans menu -->
            <div class="navbox">
                <div class="subnav">MANS</div>
                <div class="nav_mans clearfix">
                    <dl class="mans1">
                        <dt>NEW ARRIVALS</dt>
                        <dd><a href="#" title="">Vans 2013 Fall Shirts</a></dd>
                        <dd><a href="#" title="">Beach Towels</a></dd>
                        <dd><a href="#" title="">Obey Snapbacks New</a></dd>
                        <dd><a href="#" title="">Roxy Women 2013 New Boardshorts </a></dd>
                        <dd><a href="#" title="">REBEL 8 Snapbacks</a></dd>
                        <dd><a href="#" title="">Diamond Supply Co. T-Shirts</a></dd>
                        <dd><a href="#" title="">Supreme New Tees</a></dd>
                    </dl>
                    <?php foreach($mensterms as $key=>$mens):?>
                    <?php 
                        if($key==0){
                            echo "<dl class='mans2'>";
                        }else{
                            echo "<dl class='mans3'>";
                        }
                    ?>
                    <!-- <dl class="mans2"> -->
                        <dt><?php echo $mens['name'] ?></dt>
                        <?php foreach ($mens['child'] as $child):?>
                        <dd><a href="#" title=""><?php echo $child['name'] ?></a></dd>
                        <?php endforeach ?>
                    </dl>
                    <?php endforeach ?>
                    <div class="mans_ad">
                        <a href="#" title=""><img src="images/test/m1.jpg" width="220" height="140" /></a>
                        <i><a href="#" title="">I LOVE Haters ! Collections</a></i>
                    </div>
                </div>
            </div>
        </li>
        <li><a href="#">WOMENS</a>
            <!-- womens menu -->
            <div class="navbox">
                <div class="subnav">WOMENS</div>
                <div class="nav_mans clearfix">
                     <?php foreach($womensterms as $key=>$womens):?>
                        <dl class="womens1">
                        <!-- <dl class="mans2"> -->
                            <dt><?php echo $womens['name'] ?></dt>
                            <?php foreach ($womens['child'] as $child):?>
                            <dd><a href="#" title=""><?php echo $child['name'] ?></a></dd>
                            <?php endforeach ?>
                        </dl>
                    <?php endforeach ?>
                    <dl class="womens2">
                        <dt>SHOP BY</dt>
                        <dd><a href="#" title="">New Arrivals</a></dd>
                        <dd><a href="#" title="">Brands</a></dd>
                    </dl>
                    <div class="mans_ad">
                        <a href="#" title=""><img src="images/test/m1.jpg" width="220" height="140" /></a>
                        <i><a href="#" title="">I LOVE Haters ! Collections</a></i>
                    </div>
                    <div class="mans_ad">
                        <a href="#" title=""><img src="images/test/m2.jpg" width="220" height="140" /></a>
                        <i><a href="#" title="">I LOVE Haters ! Collections</a></i>
                    </div>
                </div>
            </div>
        </li>
		<li><a href="#">HATS</a>
            <!-- hats menu -->
            <div class="navbox">
                <div class="subnav">hats</div>
                <div class="nav_mans clearfix">
                    <dl class="hats1">
                        <dt>CATEGORIES</dt>
                        <?php foreach($hatsterms as $key=>$hats):?>
                            <?php foreach ($hats['child'] as $child):?>
                                <dd><a href="#" title=""><?php echo $child['name']; ?></a></dd>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </dl>
                    <dl class="hats2">
                        <dt>BRADNS</dt>
                        <?php foreach($hatsbrands as $key=>$hats):?>
                            <dd>
                                <a href="#" title=""><img src="<?php echo $hats->brand->image; ?>" width="40" height="40" /></a>
                                <span><a href="#" title=""><?php echo $hats->brand->name; ?></a></span>
                            </dd>
                        <?php endforeach ?>
                        <dt class="more"><a href="#" title="">MORE&gt;</a></dt>
                    </dl>
                    <div class="mans_ad">
                        <a href="#" title=""><img src="images/test/m2.jpg" width="220" height="140" /></a>
                        <i><a href="#" title="">I LOVE Haters ! Collections</a></i>
                    </div>
                </div>
            </div>
        </li>
        <li><a href="#">SALE</a></li>
        <li><a href="#">CONTACT</a></li>
    </ul>
</div>