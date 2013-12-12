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
        <li><a href="/mans">MANS</a>
            <!-- mans menu -->
            <div class="navbox">
                <div class="subnav">MANS</div>
                <div class="nav_mans clearfix">
                    <dl class="mans1">
                        <dt>NEW ARRIVALS</dt>
                        <?php foreach ($newarrivals as $new):?>
                        <dd><a href="<?php echo $new->url ?>" title="<?php echo $new->name; ?>"><?php echo $new->name; ?></a></dd>
                        <?php endforeach ?>
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
                        <dd><a href="<?php echo '/mans/term/cid/'.$child["id"];?>" title=""><?php echo $child['name'] ?></a></dd>
                        <?php endforeach ?>
                    </dl>
                    <?php endforeach ?>
                    <?php foreach($mensad as $women):?>
                        <?php echo $women['url']; ?>
                        <div class="mans_ad">
                            <a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><img src="<?php echo $women->image; ?>" width="220" height="140" /></a>
                            <i><a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><?php echo $women->name; ?></a></i>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </li>
        <li><a href="/womens">WOMENS</a>
            <!-- womens menu -->
            <div class="navbox">
                <div class="subnav">WOMENS</div>
                <div class="nav_mans clearfix">
                     <?php foreach($womensterms as $key=>$womens):?>
                        <dl class="womens1">
                        <!-- <dl class="mans2"> -->
                            <dt><?php echo $womens['name'] ?></dt>
                            <?php foreach ($womens['child'] as $child):?>
                            <dd><a href="<?php echo '/womens/term/cid/'.$child["id"];?>" title=""><?php echo $child['name'] ?></a></dd>
                            <?php endforeach ?>
                        </dl>
                    <?php endforeach ?>
                    <dl class="womens2">
                        <dt>SHOP BY</dt>
                        <dd><a href="#" title="">New Arrivals</a></dd>
                        <dd><a href="#" title="">Brands</a></dd>
                    </dl>
                    <?php foreach($womensad as $women):?>
                    <div class="mans_ad">
                            <a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><img src="<?php echo $women->image; ?>" width="220" height="140" /></a>
                            <i><a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><?php echo $women->name; ?></a></i>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
        </li>
		<li><a href="/hats">HATS</a>
            <!-- hats menu -->
            <div class="navbox">
                <div class="subnav">hats</div>
                <div class="nav_mans clearfix">
                    <dl class="hats1">
                        <dt>CATEGORIES</dt>
                        <?php foreach($hatsterms as $key=>$hats):?>
                            <?php foreach ($hats['child'] as $child):?>
                                <dd><a href="<?php echo '/hats/view/id/'.$child["id"];?>" title=""><?php echo $child['name']; ?></a></dd>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </dl>
                    <dl class="hats2">
                        <dt>BRADNS</dt>
                        <?php foreach($hatsbrands as $key=>$hats):?>
                            <dd>
                                <a href="<?php echo '/brands/view/id/'.$hats->brand["id"];?>" title=""><img src="<?php echo $hats->brand['image']; ?>" width="40" height="40" /></a>
                                <span><a href="<?php echo '/brands/view/id/'.$hats->brand["id"];?>" title=""><?php echo $hats->brand['name']; ?></a></span>
                            </dd>
                        <?php endforeach ?>
                        <dt class="more"><a href="/brands" title="">MORE&gt;</a></dt>
                    </dl>
                     <?php foreach($hatsad as $women):?>
                        <div class="mans_ad">
                                <a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><img src="<?php echo $women->image; ?>" width="220" height="140" /></a>
                                <i><a href="<?php echo $women->url; ?>" title="<?php echo $women->name; ?>"><?php echo $women->name; ?></a></i>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </li>
        <li><a href="/sale">SALE</a></li>
        <li><a href="/contact">CONTACT</a></li>
    </ul>
</div>