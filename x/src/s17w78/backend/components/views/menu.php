<div class="nav">
	<ul class="menu clearfix">
    	<li><a href="#">BRANDS</a>
            <!-- brands menu -->
            <div class="navbox">
                <div class="subnav">BRANDS</div>
                <dl class="nav_brands clearfix">
                    <?php foreach ($brands as $brand):?>
                        <dd>
                            <a href="/brand/view/id/<?php echo $brand->id ?>" title=""><img src="<?php echo $brand->image ?>" width="40" height="40" /></a>
                            <span><a href="/brand/view/id/<?php echo $brand->id ?>" title=""><?php echo $brand->name ?></a></span>
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
                    <dl class="womens1">
                        <dt>WOMENS CLOTHING</dt>
                        <dd><a href="#" title="">T-Shirts</a></dd>
                        <dd><a href="#" title="">Sweaters</a></dd>
                        <dd><a href="#" title="">Polos</a></dd>
                        <dd><a href="#" title="">Hoodies</a></dd>
                        <dd><a href="#" title="">Shirts</a></dd>
                        <dd><a href="#" title="">Jackets</a></dd>
                        <dd><a href="#" title="">Tank Tops</a></dd>
                        <dd><a href="#" title="">Sweatshirts</a></dd>
                        <dd><a href="#" title="">Shorts</a></dd>
                        <dd><a href="#" title="">Sweatpants</a></dd>
                        <dd><a href="#" title="">Board Shorts</a></dd>
                        <dd><a href="#" title="">Jeans</a></dd>
                    </dl>
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
                        <dd><a href="#" title="">Snapbacks</a></dd>
                        <dd><a href="#" title="">59Fifty Fitted</a></dd>
                        <dd><a href="#" title="">Beanies</a></dd>
                        <dd><a href="#" title="">Trucker Hats</a></dd>
                        <dd><a href="#" title="">Flexfit Hats</a></dd>
                    </dl>
                    <dl class="hats2">
                        <dt>BRADNS</dt>
                        <dd>
                            <a href="#" title=""><img src="images/test/b1.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b2.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Diamond Supply Co.</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b3.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b1.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Diamond Supply Co.</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b2.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b3.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b1.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b2.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
                        <dd>
                            <a href="#" title=""><img src="images/test/b3.jpg" width="40" height="40" /></a>
                            <span><a href="#" title="">Crooks & Castles</a></span>
                        </dd>
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