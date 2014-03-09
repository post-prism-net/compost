<nav>
	<a href="?logout=true" class="logout">logout</a>
	<a href="#upload" class="add">add photo</a>
</nav>

<form method="post" action="?page=0" enctype="multipart/form-data" class="uploadform" id="upload">
	<a href="#" class="close button">close</a>
	<h3>add photo: </h3>
	<span class="file button">
		1. choose file.
		<input type="file" name="file">
	</span>
	<input type="hidden" name="max_file_size" value="5242880">
	<input type="text" name="description" placeholder="2. write caption.">
	<input type="hidden" name="action" value="upload">
	<input type="submit" name="upload" value="3. upload."> 
</form>