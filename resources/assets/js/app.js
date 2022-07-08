let toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
});

$(function () {
    $(document).on({
        ajaxStart: function() { $('body').addClass("loading"); },
        ajaxStop: function() { $('body').removeClass("loading"); }
    });

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

    $('.files-list.sortable-list').sortable({
        items: ".card",
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
            let items = [];

            $(this).find('.file-card[data-id]').each(function() {
                items.push($(this).attr('data-id'));
            });

            $.ajax({
                url: '/cms/ajax/sort-files',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {items: items},
                type: "POST",
                response: 'JSON',
                success: function (response) {
                    if (response.status == 'success') {
                        toast.fire({
                            icon: 'success',
                            title: 'Файлы отсортированы',
                        });

                    } else {
                        toast.fire({
                            icon: 'error',
                            title: 'Произошла ошибка, попробуйте еще раз',
                        });
                    }
                }
            });
        }
    });

    $('[data-file-action]').click(function() {
        let id = $(this).parents('.file-card').attr('data-id');
        let data = {
            'id': id
        };

        $(this).parents('.file-card-footer').find('input,textarea').each(function() {
            data[$(this).attr('data-name')] = $(this).val();
        });

        console.log(data);

        $.ajax({
            url: '/cms/ajax/data-files',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: data,
            type: "POST",
            response: 'JSON',
            success: function (response) {
                if (response.status == 'success') {
                    toast.fire({
                        icon: 'success',
                        title: 'Данные сохранены',
                    });

                } else {
                    toast.fire({
                        icon: 'error',
                        title: 'Произошла ошибка, попробуйте еще раз',
                    });
                }
            }
        });

        return false;
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
        let element = $(this).parents('.file-card');
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

    $('input[mask]').each(function() {
        let mask = $(this).attr('mask');

        if (mask.startsWith('cost')) {
            let options = mask.split(':');

            IMask(this, {
                mask: Number,  // enable number mask

                // other options are optional with defaults below
                scale: options[1],  // digits after point, 0 for integers
                signed: false,  // disallow negative
                thousandsSeparator: '',  // any single char
                padFractionalZeros: false,  // if true, then pads zeros at end to the length of scale
                normalizeZeros: true,  // appends or removes zeros at ends
                radix: '.',  // fractional delimiter
                mapToRadix: ['.'],  // symbols to process as radix
            });
        }
    });

    $('[data-tippy]').each(function() {
        let self = $(this);
        tippy(this, {
            content: self.attr('data-tippy-content'),
            allowHTML: true,
            placement: 'top',
        });
    });

    $('[data-image-tippy]').each(function() {
        let self = $(this);
        let instance = tippy(this, {
            content: self.attr('data-tippy-content'),
            allowHTML: true,
            placement: 'left',
            onShow: () => {
                const image = new Image();
                image.style.display = 'block';
                image.src = self.attr('href');
                image.style = 'max-width: 200px; max-height: 200px;';
                instance.setContent(image);
            }
        });
    });

    $('[data-toggle=card-footer]').click(function() {
        $(this).parents('.file-card').find('.card-footer').toggleClass('hidden');
        return false;
    });
});

let imgCrop = function(element) {
    this.element = $(element);
    this.ratio = 1;
    this.coords = '';
    this.maxWidth = 766;
    this.maxHeight = 575;
    this.originalWidth = 0;
    this.originalHeight = 0;

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
                        '<div class="modal-body image-crop-container">' +
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
            self.originalWidth = this.naturalWidth;
            self.originalHeight = this.naturalHeight;

            let imageHtml = '<img src="' + self.url + '" id="' + self.imgId + '" style="max-width: 100%; height: auto;" alt="" />';

            $('#' + self.modalId + ' .modal-body').append(imageHtml);

            self.setImageSizes();

            self.aspectRatio = null;
            if(self.width && self.height) {
                self.aspectRatio = self.width / self.height;
            }

            setTimeout(function() {
                self.initJcrop();

                window.addEventListener('resize', function(e) {
                    self.resize();
                });

            }, 1);
        };

        this.img.src = this.url;
    }

    this.setImageSizes = function() {
        this.maxWidth = parseFloat($('.image-crop-container').width());
        this.maxHeight = (this.maxWidth / 4) * 3;

        $('#' + this.imgId).css('width', 'auto');
        $('#' + this.imgId).css('height', 'auto');

        if (
            this.originalWidth > this.maxWidth &&
            this.originalWidth >= this.originalHeight
        ) {
            $('#' + this.imgId).css('width', this.maxWidth);
            this.ratio = this.originalWidth / this.maxWidth;

        } else if(
            this.originalHeight > this.maxHeight &&
            this.originalWidth < this.originalHeight
        ) {
            $('#' + this.imgId).css('height', this.maxHeight);
            this.ratio = this.originalHeight / this.maxHeight;

        } else {
            this.ratio = 1;

        }
    }

    this.initJcrop = function() {
        let self = this;

        $('#' + self.imgId).Jcrop({
            aspectRatio: self.aspectRatio,
            maxSize: [self.maxWidth, self.maxHeight],
            onSelect: function(coords) {
                self.onEndCrop(coords, self.imgId);
            }
        }, function() {
            self.jcrop = this;
        });
    }

    this.resize = function(e) {
        this.jcrop.destroy();
        this.setImageSizes();
        this.initJcrop();
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
                        self.updateImageUrls();

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

    this.updateImageUrls = function() {
        let self = this;

        $('a[data-lightbox]').each(function() {
            let href = $(this).attr('href');
            let url = new URL(href);
            url.search = '?' + self.randomString(10);
            $(this).attr('href', url.href);

            if ($(this).attr('data-tippy-content')) {
                $(this).attr(
                    'data-tippy-content',
                    "<img src='" + url.href + "' style='max-width: 200px; max-height: 200px;' />"
                );
            }
        });
    }

    this.init();
}
