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

    function setItemsForDelete() {
        let items = [];
        $('input[name="items-to-delete[]"]:checked').each(function() {
            items.push($(this).attr('value'));
        });
        $('#delete-form input[name=items]').val(items.join(','));
    }

    $('a[data-toggle-filter]').click(toggleFilter);
    $('button[name=filter-close]').click(toggleFilter);

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
            let n = $("input.multiple-delete:checked").length;

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
            let n = $("input.multiple-delete:checked").length;

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

    $('.content .nav-pills li a').bind(
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

    $('.tree').each(function() {
        new tree(this);
    });

    $('[data-remove]').click(function() {
        let button = $(this);
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
                                let title = 'Файл удален';
                                if (id.split(',').length > 1) {
                                    title = 'Файлы удалены';
                                }

                                toast.fire({
                                    icon: 'success',
                                    title: filename,
                                    html: title
                                });

                                if (button.attr('type') == 'button') {
                                    button.attr('disabled', 'disabled');
                                } else {
                                    button.addClass('disabled');
                                }

                                let ids = id.split(',');
                                ids.forEach((id) => {
                                    $('.file-card[data-id=' + id + ']').remove();
                                });

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

    document.querySelectorAll('.external-listing').forEach(function (element, index) {
        new external(element);
    });

    document.querySelectorAll('.properties').forEach(function (element, index) {
        new properties(element);
    });

    $('.datetime-picker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        locale: 'ru',
        icons: {
            time: 'far fa-clock'
        }
    });

    $('.date-picker').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'ru'
    });

    document.querySelectorAll('.ajax-select').forEach(function (element, index) {
        new ajaxSelect(element);
    });

    document.querySelectorAll('.multi-values').forEach(function (element, index) {
        new multiValues(element);
    });

    document.querySelectorAll('.groups-values').forEach(function (element, index) {
        new groupsValues(element);
    });

    document.querySelectorAll('.sticky-header').forEach(function(element) {
        new stickyHeader(element);
    });

    if (document.querySelectorAll('[data-notification-id]').length) {
        document.querySelectorAll('[data-notification-id]').forEach((notification) => {
            let close = notification.querySelector('.trash');
            let notificationId = notification.dataset.notificationId;

            close.addEventListener('click', (event) => {
                event.preventDefault();

                const xhr = new XMLHttpRequest();
                xhr.open(
                    "GET",
                    "/cms/ajax/notifications?action=read&id=" + notificationId,
                    true
                );
                xhr.onload = () => {
                    if (xhr.status == 200) {
                        let json = JSON.parse(xhr.response);

                        notification.remove();

                        if ('count' in json) {
                            if (json['count'] > 0) {
                                document.querySelector('#notifications-badge').innerText = json['count'];
                            } else {
                                document.querySelector('#notifications').remove();
                            }
                        }
                    }
                };
                xhr.send();

                return false;
            });
        });
    }

    document.querySelectorAll('[multiple-delete]').forEach(function(item) {
        new multipleDelete(item);
    });

    document.querySelectorAll('.multi-values').forEach(function (element, index) {
        new multiValues(element);
    });

    document.querySelectorAll('.groups-values').forEach(function (element, index) {
        new groupsValues(element);
    });

    document.querySelectorAll('.custom-file-upload').forEach(function(element) {
        const label = element.querySelector('label');
        const input = element.querySelector('input[type=file]');
        const span = element.querySelector('span');

        input.addEventListener('change', function() {
            span.innerHTML = '&bull;';
        });
    });
});

function multipleDelete(element) {
    this.wrapper = element;
    this.button = this.wrapper.querySelector('[data-button-multiple-delete]');

    this.init = function() {
        let self = this;

        this.wrapper.querySelectorAll('input[type=checkbox].multiple-select').forEach(function(input) {
            input.addEventListener('change', function(event) {
                let n = self.wrapper.querySelectorAll('input[type=checkbox].multiple-select:checked').length;

                if (n) {
                    self.button.classList.remove('disabled');
                } else {
                    self.button.classList.add('disabled');
                }

                self.setFilesForDelete();
            });
        });

        this.button.addEventListener('click', function() {

        });
    };

    this.setFilesForDelete = function() {
        let self = this;

        let items = [];
        self.wrapper.querySelectorAll('input[type=checkbox].multiple-select:checked').forEach(function(item) {
            items.push(item.getAttribute('value'));
        });

        self.button.setAttribute('data-remove', items.join(','));
    };

    this.init();
}

let stickyHeader = function(element) {
    this.table = element;
    this.offset = 0;
    this.thead = this.table.querySelector('thead');

    this.init = function() {
        let self = this;

        let bodyRect = document.body.getBoundingClientRect(),
            elemRect = this.table.getBoundingClientRect();
        this.offset = elemRect.top - bodyRect.top;

        window.addEventListener('resize', function(event) {
            self.offset = self.table.offsetTop;
            self.updateOffset();
        });

        window.addEventListener("scroll", function(event) {
            self.updateOffset();
        });
    };

    this.updateOffset = function() {
        if (window.scrollY >= this.offset) {
            this.thead.style.transform = 'translateY(' + (window.scrollY - this.offset) + 'px)';
        } else {
            this.thead.style.transform = 'translateY(0px)'
        }
    };

    this.init();
};

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
        this.watermark = this.element.attr('data-watermark');
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
            aspectRatio: self.mode == 'free' ? null : self.aspectRatio,
            maxSize: self.mode == 'free' ? [0, 0] : [self.maxWidth, self.maxHeight],
            onSelect: function(coords) {
                self.onEndCrop(coords, self.imgId);
                self.showSize(coords);
            }
        }, function() {
            self.jcrop = this;
        });
    }

    this.showSize = function(coords) {
        let size = ((coords.w * this.ratio) / (coords.h * this.ratio)) + ' ~ ' + this.aspectRatio;
        document.querySelector('.jcrop-tracker').textContent = size;
        document.querySelector('.jcrop-tracker').style.color = 'white';
        document.querySelector('.jcrop-tracker').style.opacity = 0.3;
        document.querySelector('.jcrop-tracker').style.textShadow = '1px 1px 1px #000';
    };

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
            let formData = new FormData();
            formData.set('id', this.id);
            formData.set('coords', this.coords);
            formData.set('ratio', this.ratio);
            formData.set('width', this.width);
            formData.set('height', this.height);
            formData.set('mode', this.mode);
            formData.set('thumbnail', this.thumbnail);
            formData.set('watermark', this.watermark);
            console.log(formData);

            const xhr = new XMLHttpRequest();
            xhr.open(
                "POST",
                "/cms/ajax/resize-image",
                true
            );
            xhr.setRequestHeader(
                "X-CSRF-TOKEN",
                document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            );
            xhr.onload = () => {
                if (xhr.status == 200) {
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
            };
            xhr.onerror = () => {
                toast.fire({
                    icon: 'error',
                    title: 'Произошла ошибка. Попробуйте еще раз.'
                });
            };
            xhr.send(
                formData
            );
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

function tree(selector) {
    this.container = $(selector);

    this.init = function() {
        let self= this;

        this.type = this.container.attr('data-type');
        this.url = this.container.attr('data-url');

        if (this.type == 'sortable') {
            $("ul", this.container).sortable({
                cursor: "move",
                delay: 250,
                forcePlaceholderSize: true,
                opacity: 0.5,
                axis: "y",
                placeholder: "placeholder",
                update: function(event, ui) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: self.url,
                        data: $(this).sortable('serialize'),
                        type: "POST"
                    });
                }
            });

            $("ul li", this.container).css('cursor', 'move');
        }

        $('span.toggle-collapse', this.container).each(function() {
            $(this).bind(
                'click',
                function() {
                    self.toggle(this);
                    return false;
                }
            );
        });
    }

    this.toggle = function(element) {
        let ul = $(element).parent().parent().children('ul');

        if ($(element).hasClass('icon-plus')) {
            $(element).removeClass('icon-plus').addClass('icon-minus');
            ul.show({duration: 100, easing: 'linear'});
        } else {
            $(element).addClass('icon-plus').removeClass('icon-minus');
            ul.hide({duration: 100, easing: 'linear'});
        }
    }

    this.init();
}

function external (element) {
    this.wrapper = element;
    this.url = this.wrapper.dataset.url;
    this.parentId = this.wrapper.dataset.parentId;
    this.modal = null;
    this.modalBackdrop = null;
    this.lastUrl = null;

    this.init = function() {
        this.load();
    };

    this.load = function() {
        let self = this;

        const xhr = new XMLHttpRequest();
        xhr.open(
            "GET",
            this.url + '?modal_parent_id=' + this.parentId,
            true
        );
        xhr.onload = () => {
            self.wrapper.innerHTML = xhr.response;
            self.bind();
        };
        xhr.send();
    };

    this.bind = function() {
        let self = this;

        this.wrapper.querySelectorAll('a').forEach((element, index) => {
            element.addEventListener("click", function(event) {
                let target = element.getAttribute('href');
                let title = element.dataset.title;

                // Open modal window with data from link
                self.openModal(target, title);

                event.preventDefault();
            });
        });
    };

    this.openModal = function(url, title) {
        let self = this;

        this.lastUrl = url + '?modal_parent_id=' + this.parentId;
        this.showModal(title);

        const xhr = new XMLHttpRequest();
        xhr.open(
            "GET",
            this.lastUrl,
            true
        );
        xhr.onload = () => {
            self.modal.querySelector('.modal-body').innerHTML = xhr.response;

            // Init form elements
            self.renderFormElements();

            // Bind form buttons
            self.bindModalForm();
        };
        xhr.send();
    };

    this.bindModalForm = function() {
        let self = this;

        let form = this.modal.querySelector('form');
        form.addEventListener('submit', function(event) {
            self.sendForm();
            event.preventDefault();
        });

        form.querySelectorAll('a[confirm]').forEach(function (element) {
            let url = element.getAttribute('href');

            element.addEventListener('click', function(event) {
                event.preventDefault();

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
                            let formData = new FormData(form);

                            let xhr = new XMLHttpRequest();
                            xhr.open(
                                "GET",
                                url + '?modal_parent_id=' + this.parentId
                            );
                            xhr.onload = () => {
                                self.hideModal();
                                self.load();
                            }
                            xhr.send(formData);
                        }
                    }
                });
            });
        });
    };

    this.sendForm = function() {
        let self = this;

        let form = this.modal.querySelector('form');
        let action = form.getAttribute('action');

        let formData = new FormData(form);
        formData.set('set-referer', this.lastUrl)

        let xhr = new XMLHttpRequest();
        xhr.open(
            "POST",
            action + '?modal_parent_id=' + this.parentId
        );
        xhr.onload = () => {
            self.modal.querySelector('.modal-body').innerHTML = xhr.response;
            self.renderFormElements();
            self.bindModalForm();

            if (xhr.responseURL.indexOf(this.lastUrl) == -1) {
                self.hideModal();
            }

            self.load();
        }
        xhr.send(formData);
    };

    this.renderFormElements = function() {
        $('select', this.modal).selectpicker();

        let input = this.modal.querySelector('input[type=hidden]:not([name=_token]):not([name=_method])');
        if (input) {
            input.value = this.parentId;
        }
    };

    this.showModal = function(title) {
        let self = this;

        let modalHtml =
            "<div class=\"modal-backdrop fade show\"></div>" +
            "<div class=\"modal fade\" id=\"external-modal\" style=\"display: none;\" aria-hidden=\"true\">" +
            "<div class=\"modal-dialog modal-xl\">" +
            "<div class=\"modal-content\">" +
            "<div class=\"modal-header\">" +
            "<h4 class=\"modal-title\">" + title + "</h4>" +
            "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">" +
            "<span aria-hidden=\"true\">×</span>" +
            "</button>" +
            "</div>" +
            "<div class=\"modal-body\">" +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div>";

        document.querySelector('body').insertAdjacentHTML("beforeend", modalHtml);

        this.modal = document.querySelector('#external-modal');
        this.modalBackdrop = document.querySelector('.modal-backdrop');
        this.modal.style.display = 'block';
        document.querySelector('body').classList.add('modal-open');

        setTimeout(function () {
            self.modal.classList.add('show');
        }, 100);

        this.bindModal();
    };

    this.bindModal = function() {
        let self = this;

        // Close button
        this.modal.querySelector('.close').addEventListener('click', function(event) {
            self.hideModal();
            event.preventDefault();
        });

        // Esc to close modal
        window.addEventListener("keydown", (event) => {
            if (event.defaultPrevented) {
                return; // Do nothing if the event was already processed
            }

            switch (event.key) {
                case "Esc":
                case "Escape":
                    self.hideModal();
                    break;
                default:
                    return;
            }

            event.preventDefault();
        }, true);
    };

    this.hideModal = function() {
        let self = this;

        this.modal.classList.remove('show');

        setTimeout(function () {
            self.modalBackdrop.style.display = 'none';
            self.modal.style.display = 'none';
            document.querySelector('body').classList.remove('modal-open');

            self.removeModal();
        }, 200);
    };

    this.removeModal = function() {
        this.modalBackdrop.remove();
        this.modal.remove();
    };

    this.init();
}

function ajaxSelect(element) {
    this.wrapper = element;
    this.readonly = element.dataset.readonly;
    this.select = element.querySelector('select');
    this.input = element.querySelector('input');
    this.selectedWrapper = element.querySelector('.selected-items');

    this.init = function() {
        let self = this;

        $(this.input).autoComplete({
            minLength: 1
        });

        $(this.input).on('autocomplete.select', function (evt, item) {
            self.add(item);
            $(self.input).autoComplete('clear');
        });

        this.drawSelected();
    };

    this.add = function(item) {
        let opt = document.createElement('option');
        opt.value = item.value;
        opt.innerHTML = item.text;
        opt.setAttribute('selected', true);
        this.select.appendChild(opt);

        this.drawSelected();
    };

    this.remove = function(value) {
        for (i = 0; i < this.select.options.length; i++) {
            if (this.select.options[i].value == value)
                this.select.remove(i);
        }

        this.drawSelected();
    };

    this.drawSelected = function() {
        let self = this;
        let html = '';

        if (this.select.querySelectorAll('option').length) {
            html += "<ul>";
            this.select.querySelectorAll('option').forEach(element => {
                html += "<li>";
                if (!self.readonly) {
                    html += "<a href=\"\" data-id=\"" + element.value + "\"><span class=\"fa fa-trash text-danger\"></span></a>";
                }
                html += "<span>" + element.textContent + "</span></li>";
                html += "</li>";
            });
            html += "</ul>";
        }

        this.selectedWrapper.innerHTML = html;

        this.selectedWrapper.querySelectorAll('ul li a').forEach(a => {
            a.addEventListener('click', function (event) {
                self.remove(a.dataset.id);

                event.preventDefault();
                return false;
            });
        });
    };

    this.init();
}

function properties(element) {
    this.wrapper = element;
    this.name = this.wrapper.dataset.name;
    this.url = this.wrapper.dataset.url;
    this.rowsContainer = this.wrapper.querySelector('#properties-container');
    this.addButton = this.wrapper.querySelector('#add-property');
    this.template = this.wrapper.querySelector('.template select');

    this.init = function() {
        let self = this;

        if (this.template) {
            this.addButton.addEventListener('click', function (event) {
                event.preventDefault();

                self.addProperty();
            });

        } else {
            this.addButton.remove();
        }

        this.bindButtons();
    };

    this.bindButtons = function() {
        let self = this;

        this.wrapper.querySelectorAll('[data-row]').forEach((row) => {
            row.querySelectorAll('[data-remove-property]').forEach((removeBtn) => {
                if (!("binded" in removeBtn.dataset)) {
                    removeBtn.addEventListener('dblclick', (event) => {
                        event.preventDefault();

                        row.remove();
                    });
                    removeBtn.dataset.binded = 'true';
                }
            });

            row.querySelectorAll('[data-add-input]').forEach((addBtn) => {
                if (!("binded" in addBtn.dataset)) {
                    addBtn.addEventListener('click', (event) => {
                        self.addInputToRow(row);
                    });
                    addBtn.dataset.binded = 'true';
                }
            });

            row.querySelectorAll('.d-flex').forEach((inputRow) => {
                inputRow.querySelectorAll('[data-remove-input]').forEach((rmBtn) => {
                    if (!("binded" in rmBtn.dataset)) {
                        rmBtn.addEventListener('click', function (event) {
                            let inputRow = rmBtn.parentNode.parentNode;

                            if (!rmBtn.classList.contains('disabled')) {
                                inputRow.remove();

                                self.updateButtonsInRow(row);
                            }
                        });

                        rmBtn.dataset.binded = 'true';
                    }
                });
            });
        });
    };

    this.addInputToRow = function(row) {
        let id = row.dataset.id;

        let inputHtml =
            "<div class=\"d-flex align-items-center mt-2\">" +
            "<div class=\"\" style=\"flex-grow: 1\">" +
            "<input " +
            "class=\"form-control\" " +
            "required " +
            "type=\"text\" " +
            "name=\"" + this.name + "[" + id + "][]\" " +
            "value=\"\"" +
            "/>" +
            "</div>" +
            "<div class=\"pl-2 text-nowrap text-right\">" +
            "<span class=\"mr-2 fa fa-minus text-danger disabled\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
            "</div>" +
            "</div>";

        row.querySelector('span[data-add-input]').insertAdjacentHTML('beforebegin', inputHtml);

        this.updateButtonsInRow(row);
        this.bindButtons();
    };

    this.updateButtonsInRow = function(row) {
        let self = this;
        let count = row.querySelectorAll('.d-flex').length;

        row.querySelectorAll('.d-flex').forEach((item, index) => {
            let btn = item.querySelector('[data-remove-input]');

            if (index == 0) {
                item.classList.remove('mt-2');
            }

            if (count <= 1) {
                btn.classList.add('disabled');

            } else {
                btn.classList.remove('disabled');
            }
        });
    };

    this.addProperty = function() {
        this.addEmptyRow();
    };

    this.addEmptyRow = function() {
        this.rowsContainer.insertAdjacentHTML(
            'beforeend',
            "<div class=\"row align-items-start mb-3 w-100\" data-row=\"true\" style=\"align-content: stretch;\">" +
            "<div class=\"col-2 pt-2 property-label\"></div>" +
            "<div class=\"col property-values\" style=\"flex-grow: 1\"></div>" +
            "<div class=\"pl-2 text-right pt-2 property-actions\"><span class=\"fa fa-trash text-danger\" title=\"Двойное нажатие\" data-remove-property=\"true\" style=\"cursor: pointer\"></span></div>" +
            "</div>",
        );

        let rows = this.rowsContainer.querySelectorAll('[data-row]');
        let last = rows[rows.length- 1];

        last.querySelectorAll('[data-remove-property]').forEach((removeBtn) => {
            removeBtn.addEventListener('dblclick', (event) =>  {
                event.preventDefault();

                last.remove();
            });
        });

        let selectHtml = this.template.outerHTML;
        last.querySelector('.property-label').insertAdjacentHTML('beforeend', selectHtml);
        $('select', last).selectpicker();

        this.bindEmptyRow(last);
    };

    this.bindEmptyRow = function(row) {
        let self = this;
        row.querySelector('select').addEventListener('change', function(event) {
            let propertyId = event.target.value;

            self.getPropertyData(propertyId);
        });
    };

    this.getPropertyData = function(propertyId) {
        let xhr = new XMLHttpRequest();
        xhr.open(
            "GET",
            this.url + '?mode=info&id=' + propertyId
        );
        xhr.onload = () => {
            let json = JSON.parse(xhr.response);

            this.convertEmptyRow(json);
        }
        xhr.send();
    };

    this.convertEmptyRow = function(data) {
        let rows = this.rowsContainer.querySelectorAll('[data-row]');
        let last = rows[rows.length- 1];

        last.querySelector('.property-label').innerHTML = data.name;
        last.dataset.type = data.type;
        last.dataset.name = data.name;
        last.dataset.id = data.id;

        if  (data.type == 'boolean') {
            last.querySelector('.property-values').insertAdjacentHTML('beforeend',
                "<select " +
                "class=\"form-control\" " +
                "required " +
                "data-live-search=\"true\" " +
                "data-width=\"100%\" " +
                "size=\"10\" " +
                "data-actions-box=\"true\" " +
                "name=\"" + this.name + "[" + data.id + "]\" " +
                ">" +
                "<option value=\"false\">Нет</option>" +
                "<option value=\"true\">Да</option>" +
                "</select>"
            );

            $('select', last).selectpicker();

        } else if (data.type == 'string') {
            if (typeof data.options !== 'undefined') {
                let inputHtml = "<select " +
                    "class=\"form-control\" " +
                    "required " +
                    "data-live-search=\"true\" " +
                    "data-width=\"100%\" " +
                    "size=\"10\" " +
                    "multiple " +
                    "data-actions-box=\"true\" " +
                    "name=\"" + this.name + "[" + data.id + "][]\" " +
                    ">";
                data.options.forEach((item) => {
                    inputHtml += "<option value=\"" + item + "\">" + item + "</option>";
                });
                inputHtml += "</select>";

                last.querySelector('.property-values').insertAdjacentHTML('beforeend', inputHtml);
                $('select', last).selectpicker();

            } else {
                let inputHtml =
                    "<div class=\"d-flex align-items-center\">" +
                    "<div class=\"\" style=\"flex-grow: 1\">" +
                    "<input " +
                    "class=\"form-control\" " +
                    "required " +
                    "type=\"text\" " +
                    "name=\"" + this.name + "[" + data.id + "][]\" " +
                    "value=\"\"" +
                    "/>" +
                    "</div>" +
                    "<div class=\"pl-2 text-nowrap text-right\">" +
                    "<span class=\"mr-2 fa fa-minus text-danger disabled\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
                    "</div>" +
                    "</div>" +
                    "<span class=\"fa fa-plus text-success mt-3\" data-add-input=\"true\" style=\"cursor: pointer\"></span>"
                ;

                last.querySelector('.property-values').insertAdjacentHTML('beforeend', inputHtml);
            }

        } else {
            last.querySelector('.property-values').insertAdjacentHTML('beforeend', "<input class=\"form-control\" type=\"text\" name=\"" + this.name + "[" + data.id + "]\" value=\"\" />");
        }

        this.bindButtons();
    };

    this.init();
}

function multiValues(element) {
    this.wrapper = element;
    this.name = this.wrapper.dataset.name;
    this.rowsContainer = this.wrapper.querySelector('.values-container');
    this.addButton = this.wrapper.querySelector('.add-value');

    this.init = function() {
        let self = this;

        this.addButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.addValue();
        });

        this.bindButtons();
        this.checkAmount();
    };

    this.addValue = function() {
        let inputHtml =
            "<div class=\"d-flex align-items-center mt-2\">" +
            "<div class=\"\" style=\"flex-grow: 1\">" +
            "<input " +
            "class=\"form-control\" " +
            "required " +
            "type=\"text\" " +
            "name=\"" + this.name + "[]\" " +
            "value=\"\"" +
            "placeholder='Артикул'" +
            "/>" +
            "</div>" +
            "<div class=\"pl-2 text-nowrap text-right\">" +
            "<span class=\"mr-2 fa fa-minus text-danger\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
            "</div>" +
            "</div>";

        this.rowsContainer.insertAdjacentHTML('beforeend', inputHtml);

        this.bindButtons();
        this.checkAmount();
    };

    this.bindButtons = function() {
        let self = this;

        this.rowsContainer.querySelectorAll('.d-flex').forEach((inputRow) => {
            inputRow.querySelectorAll('[data-remove-input]').forEach((rmBtn) => {
                if (!("binded" in rmBtn.dataset)) {
                    rmBtn.addEventListener('click', function (event) {
                        let inputRow = rmBtn.closest('.d-flex');

                        if (!rmBtn.classList.contains('disabled')) {
                            inputRow.remove();
                        }

                        self.checkAmount();
                    });

                    rmBtn.dataset.binded = 'true';
                }
            });
        });
    };

    this.checkAmount = function() {
        if (this.rowsContainer.querySelectorAll('.d-flex').length == 1) {
            this.rowsContainer.querySelector('.d-flex [data-remove-input]').classList.add('disabled');
        } else {
            this.rowsContainer.querySelectorAll('.d-flex .disabled').forEach(function(item) {
                item.classList.remove('disabled');
            })
        }
    };

    this.init();
}

function groupsValues(element) {
    this.wrapper = element;
    this.name = this.wrapper.dataset.name;
    this.groupsContainer = this.wrapper.querySelector('.values-container');
    this.addButton = this.wrapper.querySelector('.add-group');
    this.checkButton = this.wrapper.querySelector('.validate');
    this.url = this.wrapper.dataset.url;

    this.init = function() {
        let self = this;

        this.addButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.addGroup();
        });

        this.checkButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.validate();
        });

        this.wrapper.querySelectorAll('[data-group-id]').forEach(function(g) {
            let id = g.dataset.groupId;

            self.bindGroup(id);
        });

        this.bindButtons();
        this.checkAmount();
    };

    this.validate = function() {
        let self = this;
        let form = this.wrapper.closest('form');
        let formData = new FormData(form);

        let xhr = new XMLHttpRequest();
        xhr.open(
            "POST",
            this.url
        );
        xhr.onload = () => {
            let json = JSON.parse(xhr.response);

            let form = self.wrapper.closest('form');

            form.closest('form').querySelectorAll('input').forEach(function(input) {
                input.classList.remove('is-invalid');
                input.classList.remove('is-valid');
            });

            form.querySelectorAll('input[placeholder=Артикул]').forEach(function(input) {
                let value = input.value;
                // input.parentNode.classList.add('was-validated');

                if (Object.values(json).includes(value)) {
                    input.classList.add('is-valid');
                } else {
                    input.classList.add('is-invalid');
                }
            });
        }
        xhr.send(formData);
    };

    this.addGroup = function() {
        let id = this.groupsContainer.querySelectorAll('.card').length;

        let inputHtml =
            '<div class="card" data-group-id="' + id + '">' +
            '<div class="card-header">' +
            '<div class="d-flex" style="column-gap: 10px">' +
            '<div style="flex-grow: 1">' +
            '<input type="text" name="' + this.name + '[' + id + '][name]" class="form-control" required value="" placeholder="Название" />' +
            '</div>' +
            '<div style="flex-shrink: 0; width: 20px; text-align: center;">' +
            '<input class="form-control" type="checkbox" name="' + this.name + '[' + id + '][primary]" value="1" />' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="card-body">' +
            '<div class="d-flex align-items-center">' +
            '<div class="" style="flex-grow: 1">' +
            '<input ' +
            'class="form-control" ' +
            'required ' +
            'type="text" ' +
            'name="' + this.name + '[' + id + '][items][]" ' +
            'value="" ' +
            'placeholder="Артикул" ' +
            '/>' +
            '</div>' +
            '<div class="pl-2 text-nowrap text-right">' +
            '<span class="mr-2 fa fa-minus text-danger" data-remove-input="true" style="cursor: pointer"></span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="card-footer">' +
            '<div class="d-flex" style="justify-content: space-between">' +
            '<span class="fa fa-plus text-success add-value" style="cursor: pointer; height: 1em"></span>' +
            '<span class="fa fa-minus text-danger remove-group" style="cursor: pointer; align-self: end"></span>' +
            '</div>' +
            '</div>' +
            '</div>'
        ;

        this.groupsContainer.insertAdjacentHTML('beforeend', inputHtml);

        this.bindGroup(id);
        this.bindButtons();
        this.checkAmount();
    };

    this.bindGroup = function(id) {
        let self = this;
        let group = this.groupsContainer.querySelector('[data-group-id="' + id + '"]');

        group.querySelector('.add-value').addEventListener('click', function(event) {
            event.preventDefault();

            let container = event.target.closest('.card');
            self.addValue(container);
        });

        group.querySelector('.remove-group').addEventListener('click', function(event) {
            event.preventDefault();

            let container = event.target.closest('.card');
            container.remove();
        });
    };

    this.addValue = function(container) {
        let groupId = container.closest('.card').dataset.groupId;
        let inputHtml =
            "<div class=\"d-flex align-items-center mt-2\">" +
            "<div class=\"\" style=\"flex-grow: 1\">" +
            "<input " +
            "class=\"form-control\" " +
            "required " +
            "type=\"text\" " +
            "name=\"" + this.name + "[" + groupId + "][items][]\" " +
            "value=\"\"" +
            "placeholder='Артикул'" +
            "/>" +
            "</div>" +
            "<div class=\"pl-2 text-nowrap text-right\">" +
            "<span class=\"mr-2 fa fa-minus text-danger\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
            "</div>" +
            "</div>";

        container.querySelector('.card-body').insertAdjacentHTML('beforeend', inputHtml);
        this.bindButtons();
        this.checkAmount();
    };

    this.bindButtons = function() {
        let self = this;

        this.groupsContainer.querySelectorAll('.card').forEach((inputRow) => {
            inputRow.querySelectorAll('[data-remove-input]').forEach((rmBtn) => {
                if (!("binded" in rmBtn.dataset)) {
                    rmBtn.addEventListener('click', function (event) {
                        let inputRow = rmBtn.closest('.d-flex');

                        if (!rmBtn.classList.contains('disabled')) {
                            inputRow.remove();
                        }

                        self.checkAmount();
                    });

                    rmBtn.dataset.binded = 'true';
                }
            });
        });
    };

    this.checkAmount = function() {
        this.groupsContainer.querySelectorAll('.card').forEach((card) => {
            let cardBody = card.querySelector('.card-body');
            if (cardBody.querySelectorAll('.d-flex').length == 1) {
                cardBody.querySelector('.d-flex [data-remove-input]').classList.add('disabled');
            } else {
                cardBody.querySelectorAll('.d-flex .disabled').forEach(function(item) {
                    item.classList.remove('disabled');
                })
            }
        });
    };

    this.init();
}

function multiValues(element) {
    this.wrapper = element;
    this.name = this.wrapper.dataset.name;
    this.rowsContainer = this.wrapper.querySelector('.values-container');
    this.addButton = this.wrapper.querySelector('.add-value');

    this.init = function() {
        let self = this;

        this.addButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.addValue();
        });

        this.bindButtons();
        this.checkAmount();
    };

    this.addValue = function() {
        let inputHtml =
            "<div class=\"d-flex align-items-center mt-2\">" +
            "<div class=\"\" style=\"flex-grow: 1\">" +
            "<input " +
            "class=\"form-control\" " +
            "required " +
            "type=\"text\" " +
            "name=\"" + this.name + "[]\" " +
            "value=\"\"" +
            "placeholder='Артикул'" +
            "/>" +
            "</div>" +
            "<div class=\"pl-2 text-nowrap text-right\">" +
            "<span class=\"mr-2 fa fa-minus text-danger\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
            "</div>" +
            "</div>";

        this.rowsContainer.insertAdjacentHTML('beforeend', inputHtml);

        this.bindButtons();
        this.checkAmount();
    };

    this.bindButtons = function() {
        let self = this;

        this.rowsContainer.querySelectorAll('.d-flex').forEach((inputRow) => {
            inputRow.querySelectorAll('[data-remove-input]').forEach((rmBtn) => {
                if (!("binded" in rmBtn.dataset)) {
                    rmBtn.addEventListener('click', function (event) {
                        let inputRow = rmBtn.closest('.d-flex');

                        if (!rmBtn.classList.contains('disabled')) {
                            inputRow.remove();
                        }

                        self.checkAmount();
                    });

                    rmBtn.dataset.binded = 'true';
                }
            });
        });
    };

    this.checkAmount = function() {
        if (this.rowsContainer.querySelectorAll('.d-flex').length == 1) {
            this.rowsContainer.querySelector('.d-flex [data-remove-input]').classList.add('disabled');
        } else {
            this.rowsContainer.querySelectorAll('.d-flex .disabled').forEach(function(item) {
                item.classList.remove('disabled');
            })
        }
    };

    this.init();
}

function groupsValues(element) {
    this.wrapper = element;
    this.name = this.wrapper.dataset.name;
    this.groupsContainer = this.wrapper.querySelector('.values-container');
    this.addButton = this.wrapper.querySelector('.add-group');
    this.checkButton = this.wrapper.querySelector('.validate');
    this.url = this.wrapper.dataset.url;

    this.init = function() {
        let self = this;

        this.addButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.addGroup();
        });

        this.checkButton.addEventListener('click', function (event) {
            event.preventDefault();

            self.validate();
        });

        this.wrapper.querySelectorAll('[data-group-id]').forEach(function(g) {
            let id = g.dataset.groupId;

            self.bindGroup(id);
        });

        this.bindButtons();
        this.checkAmount();
    };

    this.validate = function() {
        let self = this;
        let form = this.wrapper.closest('form');
        let formData = new FormData(form);

        let xhr = new XMLHttpRequest();
        xhr.open(
            "POST",
            this.url
        );
        xhr.onload = () => {
            let json = JSON.parse(xhr.response);

            let form = self.wrapper.closest('form');

            form.closest('form').querySelectorAll('input').forEach(function(input) {
                input.classList.remove('is-invalid');
                input.classList.remove('is-valid');
            });

            form.querySelectorAll('input[placeholder=Артикул]').forEach(function(input) {
                let value = input.value;
                // input.parentNode.classList.add('was-validated');

                if (Object.values(json).includes(value)) {
                    input.classList.add('is-valid');
                } else {
                    input.classList.add('is-invalid');
                }
            });
        }
        xhr.send(formData);
    };

    this.addGroup = function() {
        let id = this.groupsContainer.querySelectorAll('.card').length;

        let inputHtml =
            '<div class="card" data-group-id="' + id + '">' +
            '<div class="card-header">' +
            '<div class="d-flex" style="column-gap: 10px">' +
            '<div style="flex-grow: 1">' +
            '<input type="text" name="' + this.name + '[' + id + '][name]" class="form-control" required value="" placeholder="Название" />' +
            '</div>' +
            '<div style="flex-shrink: 0; width: 20px; text-align: center;">' +
            '<input class="form-control" type="checkbox" name="' + this.name + '[' + id + '][primary]" value="1" />' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="card-body">' +
            '<div class="d-flex align-items-center">' +
            '<div class="" style="flex-grow: 1">' +
            '<input ' +
            'class="form-control" ' +
            'required ' +
            'type="text" ' +
            'name="' + this.name + '[' + id + '][items][]" ' +
            'value="" ' +
            'placeholder="Артикул" ' +
            '/>' +
            '</div>' +
            '<div class="pl-2 text-nowrap text-right">' +
            '<span class="mr-2 fa fa-minus text-danger" data-remove-input="true" style="cursor: pointer"></span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="card-footer">' +
            '<div class="d-flex" style="justify-content: space-between">' +
            '<span class="fa fa-plus text-success add-value" style="cursor: pointer; height: 1em"></span>' +
            '<span class="fa fa-minus text-danger remove-group" style="cursor: pointer; align-self: end"></span>' +
            '</div>' +
            '</div>' +
            '</div>'
        ;

        this.groupsContainer.insertAdjacentHTML('beforeend', inputHtml);

        this.bindGroup(id);
        this.bindButtons();
        this.checkAmount();
    };

    this.bindGroup = function(id) {
        let self = this;
        let group = this.groupsContainer.querySelector('[data-group-id="' + id + '"]');

        group.querySelector('.add-value').addEventListener('click', function(event) {
            event.preventDefault();

            let container = event.target.closest('.card');
            self.addValue(container);
        });

        group.querySelector('.remove-group').addEventListener('click', function(event) {
            event.preventDefault();

            let container = event.target.closest('.card');
            container.remove();
        });
    };

    this.addValue = function(container) {
        let groupId = container.closest('.card').dataset.groupId;
        let inputHtml =
            "<div class=\"d-flex align-items-center mt-2\">" +
            "<div class=\"\" style=\"flex-grow: 1\">" +
            "<input " +
            "class=\"form-control\" " +
            "required " +
            "type=\"text\" " +
            "name=\"" + this.name + "[" + groupId + "][items][]\" " +
            "value=\"\"" +
            "placeholder='Артикул'" +
            "/>" +
            "</div>" +
            "<div class=\"pl-2 text-nowrap text-right\">" +
            "<span class=\"mr-2 fa fa-minus text-danger\" data-remove-input=\"true\" style=\"cursor: pointer\"></span>" +
            "</div>" +
            "</div>";

        container.querySelector('.card-body').insertAdjacentHTML('beforeend', inputHtml);
        this.bindButtons();
        this.checkAmount();
    };

    this.bindButtons = function() {
        let self = this;

        this.groupsContainer.querySelectorAll('.card').forEach((inputRow) => {
            inputRow.querySelectorAll('[data-remove-input]').forEach((rmBtn) => {
                if (!("binded" in rmBtn.dataset)) {
                    rmBtn.addEventListener('click', function (event) {
                        let inputRow = rmBtn.closest('.d-flex');

                        if (!rmBtn.classList.contains('disabled')) {
                            inputRow.remove();
                        }

                        self.checkAmount();
                    });

                    rmBtn.dataset.binded = 'true';
                }
            });
        });
    };

    this.checkAmount = function() {
        this.groupsContainer.querySelectorAll('.card').forEach((card) => {
            let cardBody = card.querySelector('.card-body');
            if (cardBody.querySelectorAll('.d-flex').length == 1) {
                cardBody.querySelector('.d-flex [data-remove-input]').classList.add('disabled');
            } else {
                cardBody.querySelectorAll('.d-flex .disabled').forEach(function(item) {
                    item.classList.remove('disabled');
                })
            }
        });
    };

    this.init();
}
