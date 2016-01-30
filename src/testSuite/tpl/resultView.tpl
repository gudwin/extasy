<div class="testResult">
	<div class="executionTime">
		Execution time: <?php print $loadTime ?> 
	</div>
	<?php if ( extasyTestResultColumn::STATUS_OK == $status ):?>
		<div class="success-status">
			All Ok
		</div>
	<?php elseif ( extasyTestResultColumn::STATUS_ERROR == $status) :?>
		<div class="error">
			<?php print $errorMessage ?>
		</div>
	<?php else:?>
		<div>
			No last status 
		</div> 
	<?php endif;?>
</div>