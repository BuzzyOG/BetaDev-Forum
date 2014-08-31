<?php
if (isset($this->errorArray) && count($this->errorArray) > 0)
{
?>
<div class="error">
	<strong>There Were Errors in Your Post</strong>
	<ul>
	<?php
	foreach($this->errorArray as $error){
		echo '<li>'.$error.'<li>';
	}
	?>
	</ul>
</div>
<?php
}
?>
<div class="catrow">
	Edit Post in: <?php echo $this->topicName?>
</div>
<div class="contentbox">
	<div class="postBox">
		<form action="?act=editPost&amp;id=<?php echo $_GET['id']?>" method="post" >
			<p>
				<?php secureForm("editPost"); ?>
				<input type="hidden" name="formsent" value="1" />
				Message:<br />
				<div class="postArea">
					<textarea name="message" class="post" rows="40" cols="20"><?php echo $this->message ?></textarea>
					<br />
					<div class="buttonwrapper">
						<input type="submit" class="button" name="submit" value="Edit Post" />
					</div>
				</div>
			</p>
		</form>
	</div>
</div>