let toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
});

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
        let element = $(this).parents('tr');
        let table = $(this).parents('table');
        let filename = $(this).attr('title');
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
                        success: function (response) {
                            if (response.status == 'success') {
                                toast.fire({
                                    icon: 'success',
                                    title: filename,
                                    html: 'Файл удален'
                                });

                                element.remove();
                                if (!table.find('tbody tr').length) {
                                    table.remove();
                                }

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

    $('a.crop-image').each(function() {
        new imgCrop(this);
    });
});

let imgCrop = function(element) {
    this.element = $(element);
    this.ratio = 1;
    this.coords = '';
    this.maxWidth = 766;
    this.maxHeight = 575;

    this.init = function() {
        this.width = parseInt(this.element.attr('data-width'), 10);
        this.height = parseInt(this.element.attr('data-height'), 10);
        this.thumbnail = this.element.attr('data-thumbnail');
        this.mode = this.element.attr('data-mode');
        this.url = this.element.attr('data-url');
        this.id = parseInt(this.element.attr('data-id'), 10);

        if (this.url) {
            let self = this;

            this.element.bind(
                'click',
                function() {
                    self.crop();

                    return false;
                }
            );
        } else {
            this.element.hide();
        }
    }

    this.addModalWrap = function() {
        this.modalId = this.randomString(8);

        $('.content-wrapper .content').append(
            '<div class="modal fade show" id="' + this.modalId + '" tabindex="-1" role="dialog" aria-labelledby="Crop modal" aria-hidden="true">' +
                '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h4 class="modal-title" id="myModalLabel">Выберите область на изображении</h4>' +
                            '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>' +
                            '<button type="button" class="btn btn-primary" data-submit="modal">Сохранить</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="modal-backdrop fade show"></div>'
        );
    }

    this.showModalWrap = function() {
        var self = this;

        $('body').addClass('modal-open');
        $('#' + this.modalId).fadeTo("fast", 1);

        $('#' + this.modalId + ' button.close, #' + this.modalId + ' button[data-dismiss=modal]').click(function() {
            self.removeModalWrap();

            return false;
        });

        $('#' + this.modalId + ' button[data-submit=modal]').click(function() {
            self.save();

            return false;
        });
    }

    this.removeModalWrap = function() {
        $('body').removeClass('modal-open');
        $('#' + this.modalId).remove();
        $('.modal-backdrop').remove();
    }

    this.crop = function() {
        let self = this;

        this.addModalWrap();
        this.showModalWrap();

        this.img = new Image();
        this.imgId = this.randomString(8);

        this.img.onload = function() {
            let imageHtml = '<img src="' + self.url + '" id="' + self.imgId + '" alt="" />';

            $('#' + self.modalId + ' .modal-body').append(imageHtml);

            if(
                this.width > self.maxWidth &&
                this.width >= this.height
            ) {
                $('#' + self.imgId).css('width', self.maxWidth);
                self.ratio = this.width / self.maxWidth;

            } else if(
                this.height > self.maxHeight &&
                this.width < this.height
            ) {
                $('#' + self.imgId).css('height', self.maxHeight);
                self.ratio = this.height / self.maxHeight;

            } else {
                self.ratio = 1;

            }

            let aspectRatio = null;

            if(self.width && self.height) {
                aspectRatio = self.width / self.height;
            }

            setTimeout(function() {
                $('#' + self.imgId).Jcrop({
                    aspectRatio: aspectRatio,
                    maxSize: [self.maxWidth, self.maxHeight],
                    onSelect: function(coords) {
                        self.onEndCrop(coords, self.imgId);
                    }
                });
            }, 1);
        };

        this.img.src = this.url;
    }

    this.onEndCrop = function(coords, imgId) {
        this.coords = coords.x + ';' + coords.y + ';' + coords.w + ';' + coords.h;
    }

    this.save = function() {
        let self = this;

        if(
            this.coords &&
            this.coords != '0;0;0;0'
        ) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: '/cms/ajax/resize-image/',
                data: 'id=' + this.id + '&coords=' + this.coords + '&ratio=' + this.ratio + '&width=' + this.width + '&height=' + this.height + '&mode=' + this.mode + '&thumbnail=' + this.thumbnail,
                response: 'JSON',
                success: function (response) {
                    if (response.status == 'success') {
                        toast.fire({
                            icon: 'success',
                            title: 'Изображение успешно сохранено'
                        });
                        self.removeModalWrap();

                    } else {
                        toast.fire({
                            icon: 'error',
                            title: response.title,
                        });
                    }
                },
                fail: function() {
                    toast.fire({
                        icon: 'error',
                        title: 'Произошла ошибка. Попробуйте еще раз.'
                    });
                }
            });
        } else {
            toast.fire({
                icon: 'error',
                title: 'Сначала вам необходимо выбрать область на изображении'
            });
        }
    }

    this.randomString = function(length) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

        if (! length) {
            length = Math.floor(Math.random() * chars.length);
        }

        var str = '';
        for (var i = 0; i < length; i++) {
            str += chars[Math.floor(Math.random() * chars.length)];
        }
        return str;
    }

    this.init();
}
