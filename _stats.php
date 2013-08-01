<?php require_once(dirname(__FILE__) . '/config.php'); ?>
<?php $feed = constant('FEED_CLASS'); ?>

<?php if ($feed::getFeedStats()): ?>

    <?php $usage = $feed::getFeedUsageInPercentage(); ?>
    <?php $classUsage = Utils::getClassForPercentage($usage); ?>

    Min. date : <?php echo Utils::getDateMin(); ?>
    <?php if( $usage > 0 ): ?>
        / Time remaining : <?php echo $feed::getFeedTimeRemaining(); ?>h
        / Usage : <span class="<?php echo $classUsage; ?>"><?php echo $usage; ?>%</span>
    <?php endif; ?>

<?php else: ?>

    <span class="alert">TIMEOUT</span>

<?php endif; ?>