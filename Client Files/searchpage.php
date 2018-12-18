<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">
<?php
include('realRMQClient.php');

$keyword = $_GET['keyword'];
$movies = search($keyword);
$count = $movies['totalResults'];
foreach ($movies as $movie){ echo $movie;}
echo "<hgroup class='mb20'>
		<h1>Search Results</h1>
		<h2 class='lead'><strong class='text-danger'>".$count."</strong> results were found for the search for <strong class='text-danger'>".$keyword."</strong></h2>								
	</hgroup>
    <section class='col-xs-12 col-sm-6 col-md-12'><form action= 'movieinfopage.php'>";
    
foreach ($movies['search'] as $movie) {
    echo "<article class='search-result row'>
			<div class=col-xs-12 col-sm-12 col-md-3>
				<a href='#' title=''class='thumbnail'><img src=".$movie[0]['Poster']."alt=".$movie[0]['Title']." /></a>
			</div>
			<div class='col-xs-12 col-sm-12 col-md-2'>
				<ul class='meta-search'>
					<li><i class='glyphicon glyphicon-calendar'></i> <span>".$movie[0]['Year']."</span></li>
					<li><i class='glyphicon glyphicon-time'></i> <span>'".$movie[0]['Length']." minutes'</span></li>
					<li><i class='glyphicon glyphicon-tags'></i> <span>".$movie[0]['Genre']."</span></li>
				</ul>
			</div>
			<div class='col-xs-12 col-sm-12 col-md-7'>
                <input type='hidden' name='imdbID' value=".$movie[0]['imdbID'].">
                <input type=submit class='btn btn-link' style='border:none;background-color:inherit;font-size:24px;' value=".$movie[0]['Title'].">
				<p>".$movie[0]['Plot']."</p>					
			</div>
			<span class='clearfix borda'></span>
		</article>";
}
echo "</section>";
?>
</div>