<div class="profilebox">
	<div class="userinfo profileuserinfo">
		<span class="username"><?php echo $this->UserName?></span>
		<?php
		if ($this->Avatar['url']){
		?>
			<img class="avatar" src="<?php echo $this->Avatar['url'] ?>" alt="<?php echo $this->Avatar['name']?>" />
		<?php
		}
		?>
		<ul class="info">
			<li class="header">Information</li>
			<li><strong>Group:</strong> <span style="color: <?php echo $this->currentUser['color']?>"><?php echo $this->currentUser['name']?></span></li>
			<li><strong>Registered:</strong> <?php echo $GLOBALS['super']->functions->formatDate($this->currentUser['time_added'])?></li>
			<li><strong>Last Active:</strong> <?php echo $GLOBALS['super']->functions->formatDate($this->currentUser['last_active'])?></li>
			<li><strong>Currently:</strong> <?php echo $this->Page ?></li>
		</ul>
	</div>
	<div class="profileright">
		<div class="catrow">Forum Activity</div>
			<?php
			if ($this->noActivity){
				?>
				<div class="foruminfo_box">
				<?php echo ucfirst($this->UserName)?> has had no activity on the board yet.
				</div>
				<?php
			}else{
				?>
				<div class="contentbox">
					<table class="info">
						<?php
						if ($this->TopicCount > 0){
						?>
						<tr>
							<td class="info">
								<strong>Number Of Topics</strong>
							</td>
							<td class="input">
								<?php echo $this->TopicCount?>
							</td>
						</tr>
						<?php
						}
						if ($this->PostCount > 0){
						?>
						<tr>
							<td class="info">
								<strong>Number of Replies</strong>
							</td>
							<td class="input">
								<?php echo $this->PostCount ?>
							</td>
						</tr>
						<?php
						}
						if ($this->TopicCount > 0){
						?>
						<tr>
							<td class="info">
								<strong>Topics Per Day</strong>
							</td>
							<td class="input">
								<?php echo $this->TopicsPerDay?>
							</td>
						</tr>
						<?php
						}
						if ($this->PostCount > 0){
							?>
						<tr>
							<td class="info">
								<strong>Posts Per Day</strong>
							</td>
							<td class="input">
								<?php echo $this->PostsPerDay?>
							</td>
						</tr>
						<?php
						}
						if ($this->TopicCount > 0 || $this->PostCount >0){
						?>
						<tr>
							<td class="info">
								<strong>Favorite Topic</strong><br />
								Topic Most Active in
							</td>
							<td class="input">
								<a href="index.php?act=tdisplay&amp;id=<?php echo $this->FavoriteTopic['topic_id']?>"><?php echo $this->FavoriteTopic['name'] ?></a> - (<?php echo $this->FavoriteTopic['Count']?> Messages)
							</td>
						</tr>
						<?php
						}
						if ($this->BiggestCount > 0){
						?>
						<tr>
							<td class="info">
								<strong>Biggest Topic</strong><br />
								Largest Topic Started
							</td>
							<td class="input">
								<a href="index.php?act=tdisplay&amp;id=<?php echo $this->BiggestTopic['id']?>"><?php echo $this->BiggestTopic['name']?></a> - (<?php echo $this->BiggestCount?> Messages)
							</td>
						</tr>
						<?php
						}
						if ($this->TopicCount || $this->PostCount > 0){
						?>
						<tr>
							<td class="info">
								<strong>Latest Post</strong>
							</td>
							<td class="input">
								<a href="index.php?act=tdisplay&amp;id=<?php echo $this->LastPost['topic_id']?>&amp;page=last"><?php echo $this->LastPost['topicName']?></a> - <?php echo $this->LastPostDate?>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
				</div>
			<?php
			}
			?>
		<?php
		if (count($this->Personal) > 0){
		?>
		<div class="catrow">Personal</div>
		<div class="contentbox">
			<table class="info">
				<?php
				foreach($this->Personal as $item){
					list($key, $value) = $item;
					?>
					<tr>
						<td class="info">
							<strong><?php echo $key ?></strong>
						</td>
						<td class="input">
							<?php echo $value?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
		<?php
		}
		if ($this->Signature){
		?>
		<div class="catrow noSlide">Signature</div>
		<div class="contentbox signature">
			<?php echo $this->Signature?>
		</div>
		<?php
		}
		?>
	</div>
	<div class="clearer"></div>
</div>