$(function () {
	// Filter
	function toggleFilter() {
		let filterButton = $('[data-toggle-filter]');
		let filter = $('[data-filter]');
		let parent = $(filterButton).parent();

		if (filter.hasClass('hidden')) {
			filter.removeClass('hidden');
			parent.hide();

		} else {
			filter.addClass('hidden');
			parent.show();
		}

		return false;
	}

	function resetFilter() {
		window.location.replace(window.location.pathname);
		return false;

		// let filter = $('[data-filter]');
		// let inputs = filter.find('input, select, textarea');
        //
		// inputs.each(function() {
		// 	$(this).val('');
		// 	$(this).selectpicker('refresh');
		// });
        //
		// filter.submit();
	}

	function setItemsForDelete()
	{
		let items = [];
		$('input[name="items-to-delete[]"]:checked').each(function() {
			items.push($(this).attr('value'));
		});
		$('#delete-form input[name=items]').val(items.join(','));
	}

	$('a[data-toggle-filter]').click(toggleFilter);
	$('button[name=filter-close]').click(toggleFilter);
	$('button[name=filter-reset]').click(resetFilter);

	/*
		Сортировка
	 */
	$("table.sortable-table tbody").sortable({
		items: "> tr:not(.active)",
		helper: function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
				$(this).height($(this).height());
			});
			return ui;
		},
		axis: "y",
		forcePlaceholderSize: true,
		opacity: 0.5,
		start: function(e, ui){
			ui.placeholder.height(ui.item.height());
		},
		cursor: "move",
		update: function() {
			$('button[name=save-sorting]').removeAttr('disabled');

			let items = [];
			$('input[name="sort-order[]"]').each(function() {
				items.push($(this).attr('value'));
			});
			$('#sort-form input[name=items]').val(items.join(','));
		}
	});

	$('.multiple-delete').bind(
		'change',
		function() {
			var n = $("input.multiple-delete:checked").length;

			if (n) {
				$('button[name=delete-items]').removeAttr('disabled');
			} else {
				$('button[name=delete-items]').attr('disabled', 'disabled');
			}

			setItemsForDelete();
		}
	);

	$('a.toggle-all-delete-checkbox').bind(
		'click',
		function () {
			var n = $("input.multiple-delete:checked").length;

			if (n) {
				$("input.multiple-delete").prop("checked", false);
			} else {
				$("input.multiple-delete").prop("checked", true);
			}

			$("input.multiple-delete").trigger("change");

			setItemsForDelete();

			return false;
		}
	);

	$('.nav-pills li a').bind(
		'click',
		function() {
			let tabId = $(this).attr('data-tab-id');

			Cookies.set('active_tab', tabId, {'path': window.location.pathname, sameSite: 'Lax'});
		}
	);

    let toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000
    });

    $('.toast-message').each(function() {
        toast.fire({
            icon: $(this).attr('data-icon'),
            title: $(this).attr('data-title'),
            html: $(this).html()
        });
    });

    bsCustomFileInput.init();

    bootbox.setDefaults({
        locale: "ru",
        size: "small",
    });

    $('[data-remove]').click(function() {
        let element = $(this).parent();
        let filename = element.attr('title');
        let id = this.getAttribute('data-remove');
        let url = this.getAttribute('data-url')

        bootbox.confirm({
            message: 'Вы уверены?',
            size: "small",
            buttons: {
                confirm: {
                    label: '<i class="fa fa-check"></i> Да',
                    className: 'btn-danger'
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Нет',
                    className: 'btn-default'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        url: url,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: 'id=' + id,
                        type: "POST",
                        response: 'JSON',
                        success: function (data) {
                            if (data.status == 'success') {
                                toast.fire({
                                    icon: 'success',
                                    title: filename,
                                    html: 'Файл удален'
                                });

                                element.remove();

                            } else {
                                toast.fire({
                                    icon: 'error',
                                    title: filename,
                                    html: 'Произошла ошибка при удалении файла'
                                });
                            }
                        }
                    });
                }
            }
        });

        return false;
    });

    $('[confirm]').click(function() {
        let value = this.getAttribute('confirm');
        let url = this.getAttribute('href');
        let isButton = this.hasAttribute('type') ? this.getAttribute('type') : false;

        bootbox.confirm({
            message: 'Вы уверены?',
            size: "small",
            buttons: {
                confirm: {
                    label: '<i class="fa fa-check"></i> Да',
                    className: 'btn-danger'
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Нет',
                    className: 'btn-default'
                }
            },
            callback: function(result) {
                if (result) {
                    if (isButton) {
                        $(value).submit();
                    } else {
                        window.location = url;
                    }
                }
            }
        });

        return false;
    });
});
