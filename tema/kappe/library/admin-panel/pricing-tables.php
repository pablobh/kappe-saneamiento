<?php
function pricing_tables()
{
	require_once('table_process.php');
	$wizard = new PricingWizard();
	$action = $wizard->coalesce($_GET['action']);
	$wizard->process($action, $_POST, $_SERVER['REQUEST_METHOD'] == 'POST');	
	
 	?>
<div class="admin-panel">
	<div class="top-nav">
		<div class="logo"></div>
		<div class="right-info"></div>
		<div class="clear"></div>
	</div>
	<div class="admin-panel-content full-content">
		<h2>Pricing table generator</h2>
		<div id="form-div">
			
			<?php if ($wizard->isComplete()) : ?>
				<p>The form is now complete. Clicking the button below will clear the container and start again.</p>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?action=<?php echo  $wizard->resetAction ?>">
					<input type="submit" value="Start again" />
				</form>
			<?php else: ?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=pricing_tables&amp;action=<?php echo $wizard->getStepName() ?>" id="custom" class="form">
					<div>
						<ul class="stepy-titles">
						<?php 
							$steps = $wizard->getSteps();
							foreach ($steps as $step){
								?><li <?php if($wizard->getStepProperty('title')==$step['title']): ?>class="current-step"<?php endif?>><?php echo $step['title']?><span><?php echo $step['desc']?></span></li><?php 
							}
						?>
						</ul>
					</div>
					<div class="clear"></div>
					<?php if ($wizard->getStepName() == 'step1'): ?>
					
					<div class="weblusivepanel-item">
						<h3>Choose a style</h3>
						<div class="option-item">
							<div style="float:left; width:200px;  margin-left:10px">
								<p><img src="<?php echo get_template_directory_uri()  ?>/library/admin-panel/images/pricing1.png"  alt="Style1" /> </p>
								<label for="large-2" style="display:inline; width:122px; margin-top:0px">Narrow size</label> 
								<input type="radio" style="width:20px; padding-top:10px" name="style" value="col-md-2" id="large-2" <?php if ($wizard->getValue('style') == 'style1'):?>checked="checked"<?php endif?> />
							</div>
							<div style="float:left; width:200px; margin-left:70px">
								<p><img src="<?php echo get_template_directory_uri()  ?>/library/admin-panel/images/pricing2.png"  alt="Style2" /> </p>
								<label for="large-4" style="display:inline; width:122px; margin-top:0px">3 Columns/row</label> 
								<input type="radio" style="width:20px; padding-top:10px" name="style" value="col-md-4" id="large-4" <?php if ($wizard->getValue('style') == 'style2'):?>checked="checked"<?php endif?> />
							</div>
							<div style="float:left; width:200px; margin-left:70px">
								<p><img src="<?php echo get_template_directory_uri()  ?>/library/admin-panel/images/pricing3.png"  alt="Style3" /> </p>
								<label for="large-3" style="display:inline; width:122px; margin-top:0px">4 Columns/row</label> 
								<input type="radio" style="width:20px; padding-top:10px" name="style" value="col-md-3" id="large-3" <?php if ($wizard->getValue('style') == 'style3'):?>checked="checked"<?php endif?> />
							</div>
							<div class="clear"></div> 
						</div>		
					</div>
				<?php elseif ($wizard->getStepName() == 'step2'): ?>
					<div class="weblusivepanel-item">
						<h3>Choose dimensions</h3>
						<div class="option-item">
							<span class="label">Columns:</span> 
							<input type="text" name="rows" id="rows" maxlength="2" value="<?php echo htmlSpecialChars($wizard->getValue('rows')) ?>" />
							<?php if ($wizard->isError('rows')) { ?><?php echo $wizard->getError('rows') ?><?php } ?>
						</div>
						<div class="option-item">
							<span class="label">Rows:</span> 
							<input type="text" name="columns" id="cols" maxlength="2" value="<?php echo htmlSpecialChars($wizard->getValue('columns')) ?>" />
							<?php if ($wizard->isError('columns')) { ?><?php echo $wizard->getError('columns') ?><?php } ?>
						</div>
						<a href="mo-help tooltip" title="Note: 2 additional Rows will be added for title and pricing fields. The numbers above don't include these fields."></a>
					</div>
				
				<?php elseif ($wizard->getStepName() == 'step3'): ?>
					<div class="weblusivepanel-item">
						<h3>Enter Table data</h3>
						<?php echo $wizard->generatedTable; ?>
					</div>
				
				<?php elseif ($wizard->getStepName() == 'step4'): ?>
					<div class="weblusivepanel-item">
						<h3>Get Your Code</h3>
						<div class="option-item">
						<textarea name="code" id="copycode" style="width:100%; height:300px"><?php echo $wizard->fullTable;?></textarea>
						<p style="text-align:center">Copy this text and paste where you want the table to appear.</p>
						</div>
					</div>
				<?php endif?>
				<div class="clear">
				<div style="padding:20px 0px">
					<input type="submit" class="mpanel-save" name="previous" value="&lt;&lt; Previous"<?php if ($wizard->isFirstStep()) { ?> disabled="disabled"<?php } ?> />
					<?php if(!$wizard->isLastStep()):?>
						<input type="submit" class="mpanel-save" value="Next &gt;&gt;" />
					<?php else: ?>
						<a href="?page=pricing_tables&amp;action=<?php echo $wizard->resetAction ?>" class="mpanel-reset-button" style="float:right;">Reset and start again.</a>
					<?php endif?>
					<div class="clear"></div>
				</div>			   
			</form>
			<?php endif; ?>			   
	 	</div>
	</div>
</div> 
<?php } ?>