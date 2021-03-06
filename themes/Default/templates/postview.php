<?php
foreach($this->POSTS as $post){
?>
<div class="post">
	<div class="datainfo">
		<span class="date"><?php echo $post['postdate']?></span>
		<?php
		if ($post['canEdit'] || $post['canDelete']){
			?>
			<span class="moderation">
				<?php
				if ($post['canEdit'])
					echo '<a href="?act=editPost&amp;id='.$post['id'].'">Edit</a>';
				if ($post['canEdit'] && $post['canDelete'])
					echo ' - ';
				if ($post['canDelete'])
					echo '<a href="?act=deletePost&amp;id='.$post['id'].'">Delete</a>';
				?>
			</span>
			<?php			
		}
		?>
	</div>
	<div class="userinfo">
		<?php 
		if ($post['avatar']['url'] != ""){
		?>
		<img class="avatar" src="<?php echo $post['avatar']['url']?>" alt="<?php echo $post['avatar']['name']?>" />
		<?php
		}
		?>
		<div class="leftinfo">
			<strong class="username"><?php echo $post['poster']?></strong>
			<span class="usergroup" style="color: <?php echo $post['groupcolor']?>"><?php echo $post['group']?></span><br />
			Joined: <?php echo $post['joindate']?>
		</div>
		<div class="rightinfo">
			Posts: <?php echo $post['postcount']?>
		</div>
	</div>
	<div class="postbox">
		<?php echo $post['message'];
		if ($post['editDate'] != 0){
			?>
			<div class="edited">
				Last Edited: 
			<?php
			echo $post['editDate'];
			?>
			</div>
			<?php
		}
		if($post['signature'] != ""){
		?>
		<div class="signature">
			<?php echo $post['signature'] ?>
		</div>
		<?php
		}
		?>
	</div>
	<div class="clearer"></div>
</div>
<?php
}
?>