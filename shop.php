<!doctype html>
<html>
<head>
	<link href="styles.css" rel="stylesheet" type="text/css">
<meta charset="utf-8">
	<link rel="icon" type="image/png" href="icons/leaf.png">
<title>signup</title>
</head>

<body>
<div class="banner bar">
  <div class="banner logo">
    <img class="banner" src="/images/leaf_big.png" alt="leaf-logo"/>
  </div>
  <div class="btn-group"></div>	
</div>
		<p>
			<a href="index.php">&nbsp;home&nbsp;</a>&gt
			<a href="editProfile.html">&nbsp;User Name&nbsp;</a>&gt
			<a href="shop.php">&nbsp;Shop&nbsp;</a>
		</p>
<div>
	<div width="100%" position="fixed">
		<table>
			<tr>
				<td>
					<h2>I'm looking for ...</h2>
				</td>
				<td>
					<table >
						<tr>
							
							<td>
								<p>Harvested before/after</p>
							</td>
						</tr>
						<tr>
							<td>
								<input type="radio" name="pastFuture" value="future">&nbsp;Future Harvest
								<input type="radio" name="pastFuture" value="past">&nbsp;Past Harvest
							</td>
							<td>
								<input type="date" name="harvestDate">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	</div>
	<div>
	<hr>
	<!-- Next Div, for containing user form and profile picture -->
	<div>
		<!--<div float="left" height="100%">-->
		<div class="column" style="width=30%">
			<form>
				<?php
					$handle = fopen("food.txt", "r");
					if ($handle) {
    				while (($line = fgets($handle)) !== false) {
        				// process the line read.
						$line=trim($line);
						print "<input type=\"checkbox\">$line<br>";
    				}

						fclose($handle);
					} else {
    				// error opening the file.
					}
				?>
				<input class="columnLeft" type="submit" value="Add Filter">
			</form>
		</div>	
		<!--</div>-->
		
		<div class="column" width="70%">
		<table width-max="300px">
			<tr width="100%">
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/cucumber.jpg" alt="cucumbers"/>
						<p>
							Cucumbers<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/tomato.jpg" alt="tomato"/>
						<p>
							Tomatos<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/peas.jpg" alt="peas"/>
						<p>
							Peas<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/zucchini.jpg" alt="zucchinni"/>
						<p>
							Zucchini<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
			</tr>
			<tr>
							<td>
					<div width="25%">
						<img class=itemImages src="images/items/corn.jpg" alt="corn"/>
						<p>
							Corn<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/plantain.jpg" alt="plantain"/>
						<p>
							Plantain Bananas<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/chayote.png" alt="chayote"/>
						<p>
							Chayote<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/tomatillos.jpg" alt="tomatillos"/>
						<p>
							Tomatillos<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
			</tr>
			<tr>
							<td>
					<div width="25%">
						<img class=itemImages src="images/items/ackee.jpg" alt="ackee"/>
						<p>
							Ackee<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/breadfruit.png" alt="breadfruit"/>
						<p>
							Breadfruit<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/papaya.jpg" alt="papaya"/>
						<p>
							Papaya<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/avocado.jpg" alt="avocado"/>
						<p>
							Avocado<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
			</tr>
			<tr>
							<td>
					<div width="25%">
						<img class=itemImages src="images/items/grapefruit.jpg" alt="grapefruit"/>
						<p>
							Grapefruit<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/pineapple.jpeg" alt="pineapple"/>
						<p>
							Pineapple<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class=itemImages src="images/items/pomegranate.jpg" alt="pomegranate"/>
						<p>
							Pomegranate<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
				<td>
					<div width="25%">
						<img class="itemImages" src="images/items/kiwi.jpg" alt="kiwi"/>
						<p>
							Kiwi<br>
							Expected Harvest: 10/1/18<br>
							Expected Yield: 20<br>
							<a href="orderItem.html"/>order
						</p>
					</div>
				</td>
			</tr>
			
		</table>
	</div>
	</div>
	</div>
	<div style="clear:both;"></div>
	<footer class="footer">&copy 2018, Boise State University <a href="index.php">&nbsp;home</a></footer>
</body>
</html>