function all_due_credit_resizer()
{
	var LOOP_LIMIT = 25;
	var PIXELS_TO_SPARE = 25;
	
	function start(post_id, num_of_cols, gravatar_size)
	{
		var post_width;
		var cell_width;
		var text_width;
		var obj_text_name;
		var avatar_width;
		
		if(post_id > 0){
			var obj_post_div = document.getElementById("post-" + post_id);
			
			if(obj_post_div){
				
				post_width = obj_post_div.offsetWidth;
				
				if(post_width > 0){

					//Themes often make some interesting modifications to images.
					//To ensure that normal names fit without scrollbars, we need to first
					//exclude the width of the avatar.
					avatar_width = get_avatar_width();
					
					cell_width = post_width / num_of_cols;
					text_width = cell_width - avatar_width;
					
					//To be safe, take off some pixels in case the theme is generous with borders.
					//In Firefox, the scrollbar is 15 pixels, for example. We add extra to accomodate 
					//gratuitous borders added by some themes
					text_width = text_width - PIXELS_TO_SPARE;
					
					if(text_width > 0){
						
						var i = 1;

						while(true){

							obj_text_name = document.getElementById("adc_name_" + i);

							if(obj_text_name){
								obj_text_name.style.width = text_width + "px";
							}else{ 
								break;
							}
							
							if(i >= LOOP_LIMIT)
								break;
								
							i++;
						}
					}
				}
			}
		}
		resize_containers_to_accommodate_avatars(gravatar_size);
		resize_cells_to_accommodate_avatars(gravatar_size);
	}


	function get_avatar_width(){

		var obj_avatar = document.getElementById("adc_avatar_1");
		var result;
		
		if(obj_avatar){
			result =  obj_avatar.offsetWidth;
		}
		
		return result;
	}

	function get_revised_cell_height(gravatar_size){
		var obj_cell = document.getElementById("adc_img_1");
		var result;
		var EXTRA_PIXELS = 0;
		
		if(obj_cell){
			result = obj_cell.offsetHeight;
		}
		
		if(result > gravatar_size){
			result += EXTRA_PIXELS;
		}
		
		return result;
	}

	function resize_containers_to_accommodate_avatars(gravatar_size){
		var new_height = get_revised_cell_height(gravatar_size);
		var obj_container;
		var i = 1;

		if(new_height > 0){
			while(true){
				
				obj_container = document.getElementById("adc_container_" + i);

				if(obj_container){
				
					obj_container.style.height = new_height + "px";
				}else{ 
					break;
				}
				
				if(i >= LOOP_LIMIT)
					break;
					
				i++;
			}
		}
	}

	function resize_cells_to_accommodate_avatars(gravatar_size){

		var new_height = get_revised_cell_height(gravatar_size);
		var obj_cell;
		var obj_text_name;
		var obj_text_type;
		
		if(new_height > 0){
			var i = 1;

			while(true){

				obj_cell = document.getElementById("adc_cell_" + i);
				obj_text_name = document.getElementById("adc_name_" + i);
				obj_text_type = document.getElementById("adc_type_" + i);

				if(obj_cell && obj_text_name && obj_text_type){
				
					obj_cell.style.height = new_height + "px";
					obj_text_type.style.height = new_height/2 + "px";
					obj_text_name.style.height = new_height/2 + "px";
					obj_text_name.style.lineHeight = new_height/2 + "px";
				}else{ 
					break;
				}
				
				if(i >= LOOP_LIMIT)
					break;
					
				i++;
			}
		}

		
	}
	
	this.start = start;
}
