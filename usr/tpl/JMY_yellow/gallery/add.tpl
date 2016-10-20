<div id="comments" class="comments-area blogghiamoanim fadeInUp animated" style="visibility: visible; -webkit-animation: fadeInUp;">
	<div id="respond" class="comment-respond">
		<h3 id="reply-title" class="comment-reply-title">Обратная связь</h3>
		<form action="gallery/save" method="post" id="commentform" class="comment-form" novalidate="" onsubmit="return validate(this)" enctype="multipart/form-data">
			<p class="comment-notes">Ваш электронный адрес не будет опубликован. Обязательные поля помечены <span class="required" color="red">*</span></p>				<p class="comment-form-author">
				<label for="author"><span class="screen-reader-text">[lang_old:_CAPTION]: *</span></label>
				<input id="author"  name="title" type="text" value="" aria-required="true" placeholder="[lang_old:_CAPTION]: *">
			</p>
			<p class="comment-form-email">
				<label for="email"><span class="screen-reader-text">[lang_old:_ALBUM]: *</span></label>
				<select name="album" class="text">{%ALBUMS%}</select>
			</p>			
			<p class="comment-form-url">
				<label for="url"><span class="screen-reader-text">[lang_old:_PHOTO_DATE]: </span></label>
				<input id="url" name="date" type="text" value="" placeholder="[lang_old:_PHOTO_DATE]:">
			</p>
			<div class="clear"></div>
			<p class="comment-form-comment">
				<label for="comment"><span class="screen-reader-text">[lang_old:_PHOTO_DESCRIPTION]:  *</span></label>
				{%DESCRIPTION%} 
			</p>			
			<p class="comment-form-comment">
				<label for="comment"><span class="screen-reader-text">[lang_old:_PHOTO_UPLOADING]:  *</span></label>
				<input type="file" name="urlLocal" size="65" class="text">
			</p>						
			<p class="form-allowed-tags" id="form-allowed-tags">[lang_old:_TYPE_FILES]: <b>{%ALLOW_TYPE%}</b><br>[lang_old:_MAX_FILE_SIZE] <b>{%MAX_SIZE%}</b>. <br>[lang_old:_OVER_SIZE_PHOTO]</p>					
			<p class="form-submit">
				<input name="submit" type="submit" id="submit" class="submit" value="[lang_old:_SEND]">				
			</p>
		</form>
	</div>			
</div>