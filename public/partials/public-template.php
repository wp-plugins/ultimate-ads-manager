<div>
    <?php foreach ($meta['images'] as $id => $img) :   //TODO: referral_url image unique ?>
        <a class="codeneric_uam_link" target="_blank" href="<?php echo $meta['referral_url']; ?>" title="<?php echo isset($img['title']) ?  $img['title'] : '' ;?>" data-id="<?php echo $ad_id; ?>">
            <img src="<?php echo $img['url']; ?>" alt="<?php echo isset($img['alt']) ?  $img['alt'] : '' ;?>"/>
        </a>
    <?php endforeach; ?>

</div>