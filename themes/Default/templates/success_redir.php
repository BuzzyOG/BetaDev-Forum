<div class="success">
	<strong><?php echo $this->message?></strong><br />
		<br />
		<?php
		if (isset($this->extramessage)){
			echo $this->extramessage;
			?>
			<br />
			<?php
		}			
		?>
	You are now leaving BetaDev<br />
	Click <a href="<?php echo $this->url?>">here</a> to continue.
</div>