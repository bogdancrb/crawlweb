<script></script>

<div class="proxyWidget ignore_element">
	<div class="proxy_widget_open" style="display: none"><<</div>

	<div id="proxy_top_form">
		<div class="proxy_widget_close">&times;</div>

		<div class="url-form" style="margin:0px 10px auto;">
			<button id="homeButton" class="proxy-buttons-inactive">Home</button>
			<button id="selectSingleButton" class="proxy-buttons-inactive">Select single element</button>
			<button id="selectMultipleButton" class="proxy-buttons-inactive ">Select multiple elements</button>
			<button id="clearAllSelectedButton" class="proxy-buttons-inactive">Clear selected elements</button>

			<form method="post" action="proxy/confirm" target="_top" style="float: right; width: 40%">
				<input class="proxy-input" type="text" name="url" value="<?php echo $url; ?>" autocomplete="off">
				<button id="submitForm" class="proxy-buttons-inactive">Go</button>
			</form>

			<button id="viewSelectedElementsButton" class="proxy-buttons-inactive">View selected elements</button>
		</div>
	</div>

	<div id="proxyElementsFormHtml">
		<div id="proxyElementsForm" class="sidenav">
			<div class="sidenav2 ">
				<div id="proxyElementsFormAdd" class="proxy_elements_form_add" style="display: none;">
					<div class="closeBtnFormAdd">&times;</div>

					<h5>Element CSS path*:</h5>
					<div class="errorMsg"></div>
					<textarea id="elementCssPath" rows="7" cols="10" class="proxy-textarea" name="proxyAdd[element_css_path]"></textarea>

					<h5>Element name*:</h5>
					<div class="errorMsg"></div>
					<input id="elementName" class="proxy-input" type="text" name="proxyAdd[element_name]" data-field="template_element_names" size="50" />

					<div class="proxy_element_form_bottom">
						<button id="submitElementAddButton">Submit</button>
						<button id="cancelElementAddButton">Cancel</button>
					</div>
				</div>
				<div class="proxy_elements_form_list" style="display: none;">
					<div class="closeBtnFormList">&times;</div>

					<table class="proxy_elements_table">
						<tr>
							<th>Element name</th>
							<th>Element CSS path</th>
						</tr>
						<tr class="proxy_table_no_element">
							<td colspan="2">No elements were submitted</td>
						</tr>
					</table>

					<div class="proxy_element_form_bottom">
						<button id="nextElementStepButton">Next</button>
					</div>
				</div>
				<div id="proxyElementsFormData" class="proxy_elements_form_data" style="display: none;">
					<div class="closeBtnFormData">&times;</div>

					<form id="elementsFormData" method="post">
						<h5>Category*:</h5>
						<div class="errorMsg"></div>
						<input id="categoryName" class="proxy-input" type="text" name="proxyData[category_name]" data-field="category_names" size="50" />

						<h5>Site name*:</h5>
						<div class="errorMsg"></div>
						<input id="siteName" class="proxy-input" type="text" name="proxyData[site_name]" data-field="sites_names" size="50" />

						<h5>Site main url*:</h5>
						<div class="errorMsg"></div>
						<input id="siteMainUrl" class="proxy-input" type="text" name="proxyData[site_main_url]" size="50" />

						<h5>Template name*:</h5>
						<div class="errorMsg"></div>
						<input id="templateName" class="proxy-input" type="text" name="proxyData[template_name]" data-field="template_names" size="50" />

						<h5>Content url*:</h5>
						<div class="errorMsg"></div>
						<input id="contentUrl" class="proxy-input" type="text" name="proxyData[content_url]" size="50" />

						<div class="proxy_element_form_bottom">
							<button id="saveElementsButton" type="submit">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

	<script type="text/javascript">
		var inspectorEnabledSingle = false, prevInspectorEnabledSingle = false;
		var inspectorEnabledMultiple = false, prevInspectorEnabledMultiple = false;
		var suspendInspector = false;
		var selectedElements = [];
		var submittedElements = [];
		var searchData = {};
		var autoCompleteForms = ['#proxyElementsFormAdd', '#elementsFormData'];

		function clearInspectElements()
		{
			$jQProxy('.multiple-inspect-element').removeClass('multiple-inspect-element');
			$jQProxy('.single-inspect-element').removeClass('single-inspect-element');
		}

		function clearSelectedElements()
		{
			$jQProxy('.single-selected-element').removeClass('single-selected-element');
			$jQProxy('.multiple-selected-element').removeClass('multiple-selected-element');

			selectedElements = [];
			submittedElements = [];
		}

		function disableInspector()
		{
			prevInspectorEnabledSingle = inspectorEnabledSingle;
			prevInspectorEnabledMultiple = inspectorEnabledMultiple;

			inspectorEnabledSingle = false;
			inspectorEnabledMultiple = false;
			suspendInspector = false;

			$jQProxy('a').css('cursor', '');
		}

		function reloadInspectorPreviousState()
		{
			inspectorEnabledSingle = prevInspectorEnabledSingle;
			inspectorEnabledMultiple = prevInspectorEnabledMultiple;

			if (inspectorEnabledSingle || inspectorEnabledMultiple)
			{
				$jQProxy('a').css('cursor', 'default');
			}
		}

		function loadElementsForm()
		{
			var proxyElementsFormElem = $jQProxy('#proxyElementsFormHtml');
			var proxyElementsFormHtml = proxyElementsFormElem.html();

			proxyElementsFormElem.remove();

			$jQProxy('html').append(proxyElementsFormHtml);
		}

		function loadElementIntoList(elementObj)
		{
			$jQProxy('.proxy_table_no_element').remove();

			$jQProxy('.proxy_elements_table').append(
				$jQProxy('<tr>').append(
					$jQProxy('<td>').html(elementObj.name)
						.addClass('ignore_element')
				).append(
					$jQProxy('<td>').html(elementObj.css_path)
						.addClass('ignore_element')
				).addClass('ignore_element')
			);
		}

		function refreshIgnoreElements()
		{
			$jQProxy('.proxyWidget').find('*').addClass('ignore_element');
		}

		$jQProxy(document).ready(function(){
			singleElementOptions = {selectors: ['tag', 'id', 'class', 'nthchild']};
			multipleElementOptions = {selectors: ['tag', 'id', 'class']};

			mySelectorGenerator = new CssSelectorGenerator();
			mySelectorGenerator.setIgnoreClasses([
				'single-inspect-element',
				'multiple-inspect-element',
				'single-selected-element',
				'multiple-selected-element'
			]);

			refreshIgnoreElements();
			loadElementsForm();

			$jQProxy('.proxy_widget_close').on('click', function(){
				$jQProxy('#proxy_top_form').css('width', '0px');
				$jQProxy('#proxyElementsForm').css('width', '0px');
				$jQProxy('.proxy_widget_open').css('display', '');
			});

			$jQProxy('.proxy_widget_open').on('click', function(){
				$jQProxy('#proxy_top_form').css('width', '100%');
				$jQProxy(this).css('display', 'none');
			});

			$jQProxy('#homeButton').on('click', function(){
				window.location.href = 'proxy';
			});

			$jQProxy('#selectSingleButton').on('click', function(){
				if (!suspendInspector)
				{
					clearInspectElements();

					if (!inspectorEnabledSingle)
					{
						inspectorEnabledSingle = true;
						inspectorEnabledMultiple = false;

						mySelectorGenerator.setOptions(singleElementOptions);

						$jQProxy(this).removeClass('proxy-buttons-inactive').addClass('proxy-buttons-active');
						$jQProxy('#selectMultipleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
						$jQProxy('a').css('cursor', 'default');
					}
					else
					{
						inspectorEnabledSingle = false;
						$jQProxy(this).removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
						$jQProxy('a').css('cursor', '');
					}
				}
			});

			$jQProxy('#selectMultipleButton').on('click', function(){
				if (!suspendInspector)
				{
					clearInspectElements();

					if (!inspectorEnabledMultiple)
					{
						inspectorEnabledSingle = false;
						inspectorEnabledMultiple = true;

						mySelectorGenerator.setOptions(multipleElementOptions);

						$jQProxy(this).removeClass('proxy-buttons-inactive').addClass('proxy-buttons-active');
						$jQProxy('#selectSingleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
						$jQProxy('a').css('cursor', 'default');
					}
					else
					{
						inspectorEnabledMultiple = false;
						$jQProxy(this).removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
						$jQProxy('a').css('cursor', '');
					}
				}
			});

			$jQProxy('#clearAllSelectedButton').on('click', function() {
				if (!suspendInspector)
				{
					clearInspectElements();
					clearSelectedElements();
					disableInspector();

					$jQProxy('#selectSingleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
					$jQProxy('#selectMultipleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
				}
			});

			$jQProxy('#viewSelectedElementsButton').on('click', function(){
				if (!suspendInspector)
				{
					clearInspectElements();
					disableInspector();

					$jQProxy('#proxyElementsForm').css('width', '400px');

					$jQProxy('.proxy_elements_form_list').css('display', '');
					$jQProxy('.proxy_elements_form_add').css('display', 'none');
					$jQProxy('.proxy_elements_form_data').css('display', 'none');
				}
			});

			$jQProxy('#submitElementAddButton').on('click', function(){
				var elementCssPath = $jQProxy('#elementCssPath').val();
				var elementName = $jQProxy('#elementName').val();
				var alreadySubmitted = false;

				var elementObj = {
					'name': elementName,
					'css_path': elementCssPath
				};

				submittedElements.forEach(function(elem){
					if (elem.css_path == elementObj.css_path)
					{
						alreadySubmitted = true;
					}
				});

				if (!alreadySubmitted)
				{
					submittedElements.push(elementObj);

					$jQProxy('#proxyElementsForm').css('width', '0px');

					$jQProxy('.proxy_elements_form_list').css('display', 'none');
					$jQProxy('.proxy_elements_form_add').css('display', 'none');
					$jQProxy('.proxy_elements_form_data').css('display', 'none');

					reloadInspectorPreviousState();
					clearInspectElements();

					suspendInspector = false;

					loadElementIntoList(elementObj);

					console.log(submittedElements);
				}
			});

			$jQProxy('#cancelElementAddButton, .closeBtnFormAdd').on('click', function(){
				reloadInspectorPreviousState();
				clearInspectElements();

				$jQProxy('#proxyElementsForm').css('width', '0px');

				$jQProxy('.proxy_elements_form_list').css('display', 'none');
				$jQProxy('.proxy_elements_form_add').css('display', 'none');
				$jQProxy('.proxy_elements_form_data').css('display', 'none');

				var lastSelectedElement = selectedElements.pop();
				var selectElement = inspectorEnabledSingle
					? 'single-selected-element'
					: 'multiple-selected-element';

				suspendInspector = false;

				$jQProxy(lastSelectedElement).removeClass(selectElement);
			});

			$jQProxy('#nextElementStepButton').on('click', function(){
				$jQProxy('.proxy_elements_form_list').css('display', 'none');
				$jQProxy('.proxy_elements_form_add').css('display', 'none');
				$jQProxy('.proxy_elements_form_data').css('display', '');
			});

			$jQProxy('#elementsFormData').submit(function(event){
				event.preventDefault();

				var valuesObj = $jQProxy(this).serializeArray();

				valuesObj.push({
					'name': 'proxyData[elements]',
					'value': JSON.stringify(submittedElements)
					});

				console.log($jQProxy.param(valuesObj));
				console.log(JSON.stringify(valuesObj));

				var values = $jQProxy.param(valuesObj);

				$jQProxy.ajax({
					method: 'POST',
					url: 'template/data',
					data: values,
					dataType: 'json',
					success: function(data) {
						console.log(data);
					}
				})
			});

			$jQProxy('.closeBtnFormList').on('click', function(){
				reloadInspectorPreviousState();
				clearInspectElements();

				$jQProxy('#proxyElementsForm').css('width', '0px');

				$jQProxy('.proxy_elements_form_list').css('display', 'none');
				$jQProxy('.proxy_elements_form_add').css('display', 'none');
				$jQProxy('.proxy_elements_form_data').css('display', 'none');
			});

			$jQProxy('a').on('click', function(event){
				if (inspectorEnabledSingle || inspectorEnabledMultiple)
				{
					event.preventDefault();
				}
			});

			$jQProxy('body').children().mouseover(function(e){
				if (!$jQProxy(e.target).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
				{
					var inspectElement = inspectorEnabledSingle
						? 'single-inspect-element'
						: 'multiple-inspect-element';

					$jQProxy('.' + inspectElement).removeClass(inspectElement);

					var element = e.target;
					var selector = mySelectorGenerator.getSelector(element);

					if (!$jQProxy(selector).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
					{
						$jQProxy(selector).addClass(inspectElement);
					}
				}
				return false;
			}).mouseout(function(e) {
				$jQProxy(this).removeClass('single-inspect-element');
				$jQProxy(this).removeClass('multiple-inspect-element');
			});

			$jQProxy(document).on('click', function (event) {
				if (!$jQProxy(event.target).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
				{
					var element = event.target;
					var selector = mySelectorGenerator.getSelector(element);

					var selectElement = inspectorEnabledSingle
						? 'single-selected-element'
						: 'multiple-selected-element';

					if (selectedElements.indexOf(selector, 0) < 0)
					{
						$jQProxy('#proxyElementsForm').css('width', '400px');

						$jQProxy('.proxy_elements_form_add').css('display', '');
						$jQProxy('.proxy_elements_form_list').css('display', 'none');
						$jQProxy('.proxy_elements_form_data').css('display', 'none');

						$jQProxy('#elementCssPath').val(selector);
						$jQProxy('#elementName').val('');

						$jQProxy(selector).addClass(selectElement);

						disableInspector();

						suspendInspector = true;

						selectedElements.push(selector);
						console.log(selectedElements);
					}
				}
			});

			$jQProxy('#elementName, #categoryName, #siteName, #templateName').on('input', function(){
				var inputValue = $jQProxy(this).val();
				var foundValues = [];
				var fieldName = $jQProxy(this).data('field');
				var thisElem = this;
				var parentId;

				autoCompleteForms.forEach(function(elem){
					var id = '#' + $jQProxy(thisElem).parents().attr('id');
					if ('#' + $jQProxy(thisElem).parents().attr('id') == elem) {
						parentId = elem;
					}
				});
				
				console.log(inputValue);
				console.log(parentId);

				$jQProxy.each(searchData[fieldName], function(key, elements){
					$jQProxy.each(elements, function(key, name){
						if (name.toLowerCase() == inputValue.toLowerCase())
						{
							$jQProxy('#elementName').val(name);
						}

						foundValues.push(name);
					});

				});

				console.log(foundValues);

				$jQProxy(this).autocomplete({
					source: foundValues,
					appendTo: parentId,
					classes: {
						"ui-autocomplete": "autocomplete-search ignore_element"
					}
				});
			});

			$jQProxy.ajax({
				method: 'GET',
				url: 'template/search/all',
				dataType: 'json',
				success: function(data) {
					searchData = data;
				}
			});
		});
	</script>
</div>
