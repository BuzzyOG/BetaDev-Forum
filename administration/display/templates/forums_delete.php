<div class="mainheaderbox2">
	Choose a forum to delete.
</div>
<div class="catrow">Choose a Forum:</div>
<div class="contentbox">
	<form action="" method="get" class="forumdelete">
		<div style="padding-top: 10px;">
			<input type="hidden" name="act" value="forums"/>
		    <input type="hidden" name="sub" value="delete"/>
			<select name="fid" class="select">
				<?php
				foreach($this->forum as $forum){
				?>
				<option value="<?php echo $forum['id']?>"><?php echo $forum['name']?></option>
				<?php
				}
				?>
			</select>
			<input type="submit" class="submit" value="Delete" />
		</div>
	</form>
</div>