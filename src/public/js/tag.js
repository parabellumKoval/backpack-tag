jQuery(function($){

  'use strict';

  $('#crudTable')
      .on('draw.dt', function () {

          var addButtons = document.querySelectorAll('[data-target="ak-add-tag-btn"]');
          var removeButtons = document.querySelectorAll('[data-target="remove-tag-btn"]');

          setAddButtonListeners(addButtons)
          setRemoveButtonListeners(removeButtons)
      });

    
    const setAddButtonListeners = function(buttons) {
      buttons.forEach(function(button){
        setupInlineCreateButtons($(button));

        // button.addEventListener('click', function(item) {
        //   element = $(item.target)
        //   addTagToStack(element, {text: 'text', color: 'red'})
        // })
      })
    }

    const setRemoveButtonListener = function(button) {
      button.addEventListener('click', function(item) {
          var element = $(item.target)
          var tagElement = element.parent('.ak-tag')

          var $inlineRemoveRoute = 'http://localhost:8888/admin/tag/inline/remove';
          var $dataId = element.attr('data-id')

          var $formData = {
            id: $dataId
          }

          $.ajax({
            url: $inlineRemoveRoute,
            data: $formData,
            type: 'POST',
            success: function (result) {
              // console.log(result)
              tagElement.remove()
            }
          })

        })
    }

    const setRemoveButtonListeners = function(buttons) {
      buttons.forEach(function(button){
        setRemoveButtonListener(button)
      })
    }

    function setupInlineCreateButtons(element) {
      var $fieldEntity = element.attr('data-field-related-name');
      //
      // var $inlineCreateButtonElement = $(element).parent().find('.inline-create-button');
      var $inlineCreateButtonElement = $(element);

      var $inlineModalRoute = element.attr('data-inline-modal-route');
      var $inlineModalClass = element.attr('data-inline-modal-class');
      var $parentLoadedFields = element.attr('data-parent-loaded-fields');
      var $includeMainFormFields = element.attr('data-include-main-form-fields') == 'false' ? false : (element.attr('data-include-main-form-fields') == 'true' ? true : element.attr('data-include-main-form-fields'));

      var $form = element.closest('form');

      $inlineCreateButtonElement.on('click', function (event) {

        console.log('event', event, event.target)

            //we change button state so users know something is happening.
            // var loadingText = '<span class="la la-spinner la-spin" style="font-size:18px;"></span>';
            // if ($inlineCreateButtonElement.html() !== loadingText) {
            //     $inlineCreateButtonElement.data('original-text', $inlineCreateButtonElement.html());
            //     $inlineCreateButtonElement.html(loadingText);
            // }

            //prepare main form fields to be submited in case there are some.
            if(typeof $includeMainFormFields === "boolean" && $includeMainFormFields === true) {
                var $toPass = $form.serializeArray();
            }else{
                if(typeof $includeMainFormFields !== "boolean") {
                    var $fields = JSON.parse($includeMainFormFields);
                    var $serializedForm = $form.serializeArray();
                    var $toPass = [];

                    $fields.forEach(function(value, index) {
                        $valueFromForm = $serializedForm.filter(function(field) {
                            return field.name === value
                        });
                        $toPass.push($valueFromForm[0]);

                    });

                    $includeMainFormFields = true;
                }
            }
            $.ajax({
                url: $inlineModalRoute,
                data: (function() {
                    if($includeMainFormFields) {
                        return {
                            'entity': $fieldEntity,
                            'modal_class' : $inlineModalClass,
                            'parent_loaded_fields' : $parentLoadedFields,
                            'main_form_fields' : $toPass
                        };
                    }else{
                        return {
                            'entity': $fieldEntity,
                            'modal_class' : $inlineModalClass,
                            'parent_loaded_fields' : $parentLoadedFields
                        };
                    }
                })(),
                type: 'POST',
                success: function (result) {
                    $('body').append(result);
                    triggerModal(element);

                },
                error: function (result) {
                    // Show an alert with the result
                    swal({
                        title: "error",
                        text: "error",
                        icon: "error",
                        timer: 4000,
                        buttons: false,
                    });
                }
            });

        });

    }

    function ajaxAttachTag(element, data) {
      var $inlineCreateRoute = 'http://localhost:8888/admin/tag/inline/create/attach';
      var $dataId = element.attr('data-id')
      var $dataType = element.attr('data-type')

      var $formData = {
        tag_id: data.id,
        taggable_id: $dataId,
        taggable_type: $dataType,
      }

      $.ajax({
        url: $inlineCreateRoute,
        data: $formData,
        type: 'POST',
        success: function (result) {
          console.log(result)
        }
      })
    }

    function addTagToStack(element, data) {
      // console.log('addTagToStack', data)
      // $(element).insertBefore()
      var removeBtnHtml = '<button class="ak-tag-remove-btn" data-id="' + data.taggable.id + '" data-target="remove-tag-btn">X</button>';
      $('<span class="ak-tag" style="background: ' + data.tag.color + ';">' + data.tag.text + removeBtnHtml +'</span>')
      .insertBefore(element)

      var removeBtnFromDom = $('button[data-id="' + data.taggable.id + '"]')[0]
      setRemoveButtonListener(removeBtnFromDom)
    }

    function triggerModal(element) {
        var $fieldName = element.attr('data-field-related-name');
        var $modal = $('#inline-create-dialog');
        var $modalSaveButton = $modal.find('#saveButton');
        var $modalCancelButton = $modal.find('#cancelButton');
        var $form = $(document.getElementById($fieldName+"-inline-create-form"));
        var $inlineCreateRoute = element.attr('data-inline-create-route');
        var $ajax = element.attr('data-field-ajax') == 'true' ? true : false;
        var $force_select = (element.attr('data-force-select') == 'true') ? true : false;

        var $dataId = element.attr('data-id');
        var $dataType = element.attr('data-type');

        $modal.modal();

        // initializeFieldsWithJavascript($form);

        $modalCancelButton.on('click', function () {
            $($modal).modal('hide');
        });

        //when you hit save on modal save button.
        $modalSaveButton.on('click', function () {

            $form = document.getElementById($fieldName+"-inline-create-form");

            //this is needed otherwise fields like ckeditor don't post their value.
            $($form).trigger('form-pre-serialize');

            var $formData = new FormData($form);
            $formData.append('taggable_id', $dataId);
            $formData.append('taggable_type', $dataType);

            // console.log('$formData', $formData)

            //we change button state so users know something is happening.
            //we also disable it to prevent double form submition
            var loadingText = '<i class="la la-spinner la-spin"></i> saving...';
            if ($modalSaveButton.html() !== loadingText) {
                $modalSaveButton.data('original-text', $(this).html());
                $modalSaveButton.html(loadingText);
                $modalSaveButton.prop('disabled', true);
            }


            $.ajax({
                url: $inlineCreateRoute,
                data: $formData,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (result) {

                    addTagToStack(element, result)

                    $modal.modal('hide');

                    new Noty({
                        type: "info",
                        text: 'Тег прикреплен успешно',
                    }).show();
                },
                error: function (result) {

                    var $errors = result.responseJSON.errors;

                    let message = '';
                    for (var i in $errors) {
                        message += $errors[i] + ' \n';
                    }

                    new Noty({
                        type: "error",
                        text: '<strong>Произошла ошибка</strong><br> '+message,
                    }).show();

                    //revert save button back to normal
                    $modalSaveButton.prop('disabled', false);
                    $modalSaveButton.html($modalSaveButton.data('original-text'));
                }
            });
        });

        $modal.on('hidden.bs.modal', function (e) {
            $modal.remove();

            //when modal is closed (canceled or success submited) we revert the "+ Add" loading state back to normal.
            var $inlineCreateButtonElement = $(element).parent().find('.inline-create-button');
            $inlineCreateButtonElement.html($inlineCreateButtonElement.data('original-text'));
        });


        $modal.on('shown.bs.modal', function (e) {
            $modal.on('keyup',  function (e) {
            if($modal.is(':visible')) {
                var key = e.which;
                    if (key == 13) { //This is an ENTER
                    e.preventDefault();
                    $modalSaveButton.click();
                }
            }
            return false;
        });
      });
    }

})