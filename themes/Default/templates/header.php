<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="shortcut icon" type="image/x-icon" href="themes/Default/images/favicon.ico"> 
		<?php
		$i = 1;
		foreach( $this->STYLESHEETS as $style ) {
			if( isset( $style[ 'path' ] ) ) {
				?><link rel="stylesheet" type="text/css" href="<?= $style[ 'path' ]; ?>"><?php
			}
			if( isset( $style[ 'text' ] ) ) {
				?><style><?= $style[ 'text' ]; ?></style><?php
			}
			print $i++ === count( $this->STYLESHEETS ) && count( $this->SCRIPTS ) == 0 ? "\n" : "\n		";
		}
		$i = 1;
		foreach( $this->SCRIPTS as $script ) {
			if( isset( $script[ 'path' ] ) ) {
				?><script type="text/javascript" src="<?= $script[ 'path' ]; ?>"></script><?php
			}
			if( isset( $script[ 'text' ] ) ) {
				?><script type="text/javascript"><?= $script[ 'text' ]; ?></script><?php
			}
			print $i++ === count( $this->SCRIPTS ) ? "\n" : "\n		";
		}
		?>
		<title><?php echo $this->TITLE?></title>
	</head>
<body>
	<div id="container">
		<a name="top"></a>
		<div id="logostrip">
			<img src="themes/Default/images/header.jpg" alt="">
		</div>
		<div id="container2">