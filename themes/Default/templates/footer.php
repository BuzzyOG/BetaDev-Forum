			<div id="forumfooterbar">
				<div class="left">
				<?php
					$totalUsers = count($this->UsersOnPage);
				?>
					<a href="#top" id="gotop"><span>Top</span></a>
					<a id="numusersonpage"><span><?php echo $totalUsers?> Users, <?php echo $this->GuestsOnPage?> Guests On This Page</span></a>
					<?php if ($totalUsers > 0){
					?>
					<span id="shortonline">
						<?php
						$usersInShort = min(4,$totalUsers);
						for ($i = 0; $i<$usersInShort; $i++){
							$comma = "";
							if ($i != $usersInShort-1){
								$comma = ", ";
							}
							echo $this->UsersOnPage[$i].$comma;
						}
						if($usersInShort < $totalUsers){
						?>						
						 ...
						<a id="showmoreonline">Show All</a>
						<?php
						}
						?>
					</span>
					<?php
					}
					?>
				</div>
				<div class="right">
					<span><?php echo $this->time?></span>
				</div>
			</div>
			<div id="usersonpage">
				<a id="showlessonline">Show Less</a>
				<?php
				foreach($this->UsersOnPage as $index => $user){
					$comma = "";
					if ($index != $totalUsers-1){
						$comma = ", ";
					}
					echo $user.$comma;
				}
				?>
			</div>
		</div>
		<div id="copyright">
			Powered By <a href="http://betadev.co.uk">BetaDev</a> Forum software <span id="version"> <?php echo $this->VERSION?></span>.<br />
			Developed by Cblair91
		</div>
	</div>
</body>
</html>