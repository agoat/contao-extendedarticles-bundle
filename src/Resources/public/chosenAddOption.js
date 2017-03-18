window.addEvent("domready", function() {

	$$(".tl_chosen_add_option").chosen();
	
	$$(".tl_chosen_add_option input").addEvent("keyup", function(evt){
		
		if (evt.target.value === '')
		{
			return;
		}
		
		if (evt.key === 'enter' || evt.key === 'tab') {
			evt.preventDefault();
			for (let [key, option] of Object.entries(evt.target.getParent().getParent(".tl_chosen_add_option").getPrevious("select.tl_chosen_add_option").getChildren())) {
				if (option.value === evt.target.value) {
					return;
				}
			}
				
			evt.target.getParent(".tl_chosen_add_option").getPrevious("select.tl_chosen_add_option").appendHTML(
				'<option value="'+evt.target.get('value')+'" selected>'+evt.target.get('value')+'</option>'
			);
			
			$$(".tl_chosen_add_option").fireEvent('liszt:updated');
			evt.target.getParent(".tl_chosen_add_option").fireEvent('mouseleave');
			event.target.fireEvent('blur').blur();
		}
	})
	
	$$(".tl_chosen_add_option input").addEvent("keydown", function(evt){

		if (evt.key === 'tab') {
			evt.preventDefault();
		}
	})
	
	if ($$(".tl_chosen_add_option").length > 0)
	{
		Locale.define(Locale.getCurrent().name, 'Chosen', {
			'noResults': $$(".tl_chosen_add_option")[0].get('data-noresult')
		})
		
	}
})
