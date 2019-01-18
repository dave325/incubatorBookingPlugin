<script type="text/javascript">
	function showHelp( divID ) {
		var helpDiv = document.getElementById( divID );

		if ( helpDiv.style.display == 'none' ) {
			var finalShow = "block";
		} else {
			var finalShow = "none";
		}
		helpDiv.style.display = finalShow;
	}
</script>
<style type="text/css">
	.emailHelpMain {
		width: 600px;
		float: left;
		clear: both;
		border-radius: 8px;
		box-shadow: 5px 5px 15px #555555;
		padding: 10px;
		margin: 10px;
		background-color: #FFF;
		border: thin solid #000;
	}
	
	.emailHelp {
		position: relative;
		display: table;
	}
	
	.emailHelp .helpRow {
		display: table-row;
	}
	
	.emailHelp .helpRow:nth-child(odd) {
		background: #DDD;
	}
	
	.emailHelp .helpRow:nth-child(1) {
		background: #000;
		color: #FFF;
		font-weight: bold;
		font-size: 1.1em;
	}
	
	.emailHelp .helpRow div:nth-child(odd) {
		font-weight: bold;
	}
	
	.emailHelp .helpRow div {
		display: table-cell;
		padding: 3px;
		width: 150px;
	}
	
	.emailForm {
		position: relative;
		top: 0px;
	}
	
	.helpFormMain {
		float: left;
		clear: left;
	}
	
	#helpButton {
		cursor: pointer;
	}
	
	.helpRow_left {
		width: 300px;
		float: left;
	}
	
	.helpRow_right {
		width: 300px;
		float: right;
	}
	
	.mainText {
		width: 600px;
	}
</style>