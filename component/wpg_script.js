
		var limit_rss = 5;
		
		function wpg_test_url(t,id) {
			var h = jQuery(t);
			h.text("Wait..").prop("disabled",true);
			jQuery("#"+id).prop("disabled",true);
		
			var url = jQuery("#"+id).val();
			var data = {
				'action':'wpg_ajax_test_rss',
				'url':url
			};			
			jQuery.ajax({
				data:data,
				type:"POST",
				timeout: 15000,
				url:wpg_object.ajax_url,
				success:function(res) {
					alert(res);
					h.text("Test").prop("disabled",false);
					jQuery("#"+id).prop("disabled",false);
				},
				error: function(request, status, err) {
					//alert("Ops sorry : "+status+" "+err);
					alert(status+' '+err);
				}
			});
		}
		
		function wpg_get_rss(url,id,offset,limit) {
			var h = jQuery("#poststuff");
			h.find("#"+id).html("<div class='wpg_alert'>Fetching : "+url+"...</div>");

			var data = {
				'action':'wpg_load_rss',
				'url':url,
				'offset':offset,
				'limit':limit,
				'id':id
			};			
			jQuery.ajax({
				data:data,
				type:"POST",
				timeout: 15000,
				url:wpg_object.ajax_url,
				success:function(res) {
					h.find("#"+id).html(res);
					h.find("#"+id).find(".wpg_alert").slideUp();
				},
				error: function(request, status, err) {
					//alert("Ops sorry : "+status+" "+err);
					h.find("#"+id).find(".wpg_alert").html("Unfortunately failed to load, "+status+" "+err);
				}
			});
		}
		
		function wpg_trigger_grab(url) {
			jQuery("#poststuff #url_source").val(url);
			wpg_js_grab();
		}
		
		jQuery(function() {		
			jQuery(".wpg_rss").each(function(i) {
				var u = jQuery(this).attr("data-url");
				var id = jQuery(this).attr("id");
				if(u) {
					wpg_get_rss(u,id,'0',limit_rss);
				}
			});
			jQuery(".wpg_example_link").click(function() {
				var ur = jQuery(this).attr('title');
				jQuery("#url_source").val(ur);
			});			
		});
		
		function wpg_js_grab() {
						
			var url_position = jQuery("#url_source").offset().top-100;
			
			var url = jQuery("#url_source").val();
			if(url=='') {
				alert("Grabber aborted !, You did not fill any url :(");
				jQuery("#url_source").attr('placeholder','Hei im hungry !, please input url here...?').focus();
				return false;
			}
			jQuery("html, body").animate({ scrollTop: url_position }, "slow");
			jQuery("#wpg_grab_button").text("Wait..").prop("disabled",true);
			jQuery("#url_source").prop("disabled",true);
			var data = {
				'action':'wpg_grab_content',				
				'url':url
			};
			jQuery.ajax({
				data:data,
				type:"POST",
				timeout: 15000,
				url:wpg_object.ajax_url,
				success:function(res) {
					if(res.api_status==0) {
						alert(res.api_message);
						jQuery("#wpg_grab_button").text("Grab URL").prop("disabled",false);
						jQuery("#url_source").prop("disabled",false);
						return false;
					}
				
					jQuery("#poststuff #title").focus().val(res.title).blur();				
					tinyMCE.get('content').setContent(res.content);
					jQuery("#new-tag-post_tag").val(res.tag);
					jQuery("input.tagadd").click();
					jQuery("#new-tag-post_tag").blur();
					jQuery("#wpg_grab_button").text("Grab URL").prop("disabled",false);
					jQuery("#url_source").prop("disabled",false);					
					setTimeout(function() {
						jQuery("html, body").animate({ scrollTop: 0 }, "slow");
					},600);	
				},
				error: function(request, status, err) {
					alert("Ops sorry : "+status+" "+err);
					jQuery("#wpg_grab_button").text("Grab URL").prop("disabled",false);
					jQuery("#url_source").prop("disabled",false);
				}
			});
		}