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
				<input id="urlAddress" class="proxy-input" type="text" name="url" value="<?php echo $url; ?>" autocomplete="off">
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

					<div id="elementUrlOnly" style="display: none">
						<h5>Extract only URL*:</h5>
						<div class="errorMsg"></div>
						<form>
							<input type="radio" name="proxyAdd[element_url_only]" value="yes">Yes<br>
							<input type="radio" name="proxyAdd[element_url_only]" value="no" checked>No<br>
						</form>
					</div>

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

		(function($)
		{
			function clearInspectElements()
			{
				$('.multiple-inspect-element').removeClass('multiple-inspect-element');
				$('.single-inspect-element').removeClass('single-inspect-element');
			}

			function clearSelectedElements()
			{
				$('.single-selected-element').removeClass('single-selected-element');
				$('.multiple-selected-element').removeClass('multiple-selected-element');

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

				$('a').css('cursor', '');
			}

			function reloadInspectorPreviousState()
			{
				inspectorEnabledSingle = prevInspectorEnabledSingle;
				inspectorEnabledMultiple = prevInspectorEnabledMultiple;

				if (inspectorEnabledSingle || inspectorEnabledMultiple)
				{
					$('a').css('cursor', 'default');
				}
			}

			function loadElementsForm()
			{
				var proxyElementsFormElem = $('#proxyElementsFormHtml');
				var proxyElementsFormHtml = proxyElementsFormElem.html();

				proxyElementsFormElem.remove();

				$('html').append(proxyElementsFormHtml);
			}

			function loadElementIntoList(elementObj)
			{
				$('.proxy_table_no_element').remove();

				$('.proxy_elements_table').append(
					$('<tr>').append(
						$('<td>').html(elementObj.name)
							.addClass('ignore_element')
					).append(
						$('<td>').html(elementObj.css_path)
							.addClass('ignore_element')
					).addClass('ignore_element')
				);
			}

			function refreshIgnoreElements()
			{
				$('.proxyWidget').find('*').addClass('ignore_element');
			}

			function loadFormUrls()
			{
				$('#siteMainUrl').val($('#urlAddress').val().match(/[http]+[s]*[://]+[www.]*[a-zA-Z]+[.]+[a-zA-Z]+[.]*[a-zA-Z]*/gim));
				$('#contentUrl').val($('#urlAddress').val());
			}

			$(document).ready(function ()
			{
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
				loadFormUrls();

				$('.proxy_widget_close').on('click', function ()
				{
					$('#proxy_top_form').css('width', '0px');
					$('#proxyElementsForm').css('width', '0px');
					$('.proxy_widget_open').css('display', '');
				});

				$('.proxy_widget_open').on('click', function ()
				{
					$('#proxy_top_form').css('width', '100%');
					$(this).css('display', 'none');
				});

				$('#homeButton').on('click', function ()
				{
					window.location.href = 'proxy';
				});

				$('#selectSingleButton').on('click', function ()
				{
					if (!suspendInspector)
					{
						clearInspectElements();

						if (!inspectorEnabledSingle)
						{
							inspectorEnabledSingle = true;
							inspectorEnabledMultiple = false;

							mySelectorGenerator.setOptions(singleElementOptions);

							$(this).removeClass('proxy-buttons-inactive').addClass('proxy-buttons-active');
							$('#selectMultipleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
							$('a').css('cursor', 'default');
						}
						else
						{
							inspectorEnabledSingle = false;
							$(this).removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
							$('a').css('cursor', '');
						}
					}
				});

				$('#selectMultipleButton').on('click', function ()
				{
					if (!suspendInspector)
					{
						clearInspectElements();

						if (!inspectorEnabledMultiple)
						{
							inspectorEnabledSingle = false;
							inspectorEnabledMultiple = true;

							mySelectorGenerator.setOptions(multipleElementOptions);

							$(this).removeClass('proxy-buttons-inactive').addClass('proxy-buttons-active');
							$('#selectSingleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
							$('a').css('cursor', 'default');
						}
						else
						{
							inspectorEnabledMultiple = false;
							$(this).removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
							$('a').css('cursor', '');
						}
					}
				});

				$('#clearAllSelectedButton').on('click', function ()
				{
					if (!suspendInspector)
					{
						clearInspectElements();
						clearSelectedElements();
						disableInspector();

						$('#selectSingleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
						$('#selectMultipleButton').removeClass('proxy-buttons-active').addClass('proxy-buttons-inactive');
					}
				});

				$('#viewSelectedElementsButton').on('click', function ()
				{
					if (!suspendInspector)
					{
						clearInspectElements();
						disableInspector();

						$('#proxyElementsForm').css('width', '400px');

						$('.proxy_elements_form_list').css('display', '');
						$('.proxy_elements_form_add').css('display', 'none');
						$('.proxy_elements_form_data').css('display', 'none');
					}
				});

				$('#submitElementAddButton').on('click', function ()
				{
					var elementCssPath = $('#elementCssPath').val();
					var elementName = $('#elementName').val();
					var elementUrlOnly = $('#elementUrlOnly form input:checked').val();
					var alreadySubmitted = false;

					var elementObj = {
						'name': elementName,
						'css_path': elementCssPath,
						'url_only': elementUrlOnly
					};

					submittedElements.forEach(function (elem)
					{
						if (elem.css_path == elementObj.css_path)
						{
							alreadySubmitted = true;
						}
					});

					if (!alreadySubmitted)
					{
						submittedElements.push(elementObj);

						$('#proxyElementsForm').css('width', '0px');

						$('.proxy_elements_form_list').css('display', 'none');
						$('.proxy_elements_form_add').css('display', 'none');
						$('.proxy_elements_form_data').css('display', 'none');

						reloadInspectorPreviousState();
						clearInspectElements();

						suspendInspector = false;

						loadElementIntoList(elementObj);

						console.log(submittedElements);
					}
				});

				$('#cancelElementAddButton, .closeBtnFormAdd').on('click', function ()
				{
					reloadInspectorPreviousState();
					clearInspectElements();

					$('#proxyElementsForm').css('width', '0px');

					$('.proxy_elements_form_list').css('display', 'none');
					$('.proxy_elements_form_add').css('display', 'none');
					$('.proxy_elements_form_data').css('display', 'none');

					$('#elementUrlOnly').css('display', 'none');

					var lastSelectedElement = selectedElements.pop();
					var selectElement = inspectorEnabledSingle
						? 'single-selected-element'
						: 'multiple-selected-element';

					suspendInspector = false;

					$(lastSelectedElement).removeClass(selectElement);
				});

				$('#nextElementStepButton').on('click', function ()
				{
					$('.proxy_elements_form_list').css('display', 'none');
					$('.proxy_elements_form_add').css('display', 'none');
					$('.proxy_elements_form_data').css('display', '');
				});

				$('#elementsFormData').submit(function (event)
				{
					event.preventDefault();

					var valuesObj = $(this).serializeArray();

					valuesObj.push({
						'name': 'proxyData[elements]',
						'value': JSON.stringify(submittedElements)
					});

					console.log($.param(valuesObj));
					console.log(JSON.stringify(valuesObj));

					var values = $.param(valuesObj);

					$.ajax({
						method: 'POST',
						url: 'template/data',
						data: values,
						dataType: 'text',
						success: function (msg)
						{
							alert(msg);

							reloadInspectorPreviousState();
							clearInspectElements();

							$('#proxyElementsForm').css('width', '0px');

							$('.proxy_elements_form_list').css('display', 'none');
							$('.proxy_elements_form_add').css('display', 'none');
							$('.proxy_elements_form_data').css('display', 'none');
						}
					})
				});

				$('.closeBtnFormList, .closeBtnFormData').on('click', function ()
				{
					reloadInspectorPreviousState();
					clearInspectElements();

					$('#proxyElementsForm').css('width', '0px');

					$('.proxy_elements_form_list').css('display', 'none');
					$('.proxy_elements_form_add').css('display', 'none');
					$('.proxy_elements_form_data').css('display', 'none');
				});

				$('a').on('click', function (event)
				{
					if (inspectorEnabledSingle || inspectorEnabledMultiple)
					{
						event.preventDefault();

						$('#elementUrlOnly').css('display', '');
					}
				});

				$('body').children().mouseover(function (e)
				{
					if (!$(e.target).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
					{
						var inspectElement = inspectorEnabledSingle
							? 'single-inspect-element'
							: 'multiple-inspect-element';

						$('.' + inspectElement).removeClass(inspectElement);

						var element = e.target;
						var selector = mySelectorGenerator.getSelector(element);

						if (!$(selector).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
						{
							$(selector).addClass(inspectElement);
						}
					}
					return false;
				}).mouseout(function (e)
				{
					$(this).removeClass('single-inspect-element');
					$(this).removeClass('multiple-inspect-element');
				});

				$(document).on('click', function (event)
				{
					if (!$(event.target).hasClass('ignore_element') && (inspectorEnabledSingle || inspectorEnabledMultiple))
					{
						var element = event.target;
						var selector = mySelectorGenerator.getSelector(element);

						var selectElement = inspectorEnabledSingle
							? 'single-selected-element'
							: 'multiple-selected-element';

						if (selectedElements.indexOf(selector, 0) < 0)
						{
							$('#proxyElementsForm').css('width', '400px');

							$('.proxy_elements_form_add').css('display', '');
							$('.proxy_elements_form_list').css('display', 'none');
							$('.proxy_elements_form_data').css('display', 'none');

							$('#elementCssPath').val(selector);
							$('#elementName').val('');

							$(selector).addClass(selectElement);

							disableInspector();

							suspendInspector = true;

							selectedElements.push(selector);
							console.log(selectedElements);
						}
					}
				});

				$('#elementName, #categoryName, #siteName, #templateName').on('input', function ()
				{
					var inputValue = $(this).val();
					var foundValues = [];
					var fieldName = $(this).data('field');
					var thisElem = this;
					var parentId;

					autoCompleteForms.forEach(function (elem)
					{
						var id = '#' + $(thisElem).parents().attr('id');
						if ('#' + $(thisElem).parents().attr('id') == elem)
						{
							parentId = elem;
						}
					});

					console.log(inputValue);
					console.log(parentId);

					$.each(searchData[fieldName], function (key, elements)
					{
						$.each(elements, function (key, name)
						{
							if (name.toLowerCase() == inputValue.toLowerCase())
							{
								$('#elementName').val(name);
							}

							foundValues.push(name);
						});

					});

					console.log(foundValues);

					$(this).autocomplete({
						source: foundValues,
						appendTo: parentId,
						classes: {
							"ui-autocomplete": "autocomplete-search ignore_element"
						}
					});
				});

				$.ajax({
					method: 'GET',
					url: 'template/search/all',
					dataType: 'json',
					success: function (data)
					{
						searchData = data;
					}
				});
			});
		})(jQProxy);
	</script>
</div>
