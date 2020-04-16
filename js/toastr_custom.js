toastr.options = {
    closeButton: true,
    preventDuplicates: false,
};
processingToastr = null;
fixedToastr = null;
var showProcessing = function (text) {
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    processingToastr = toastr.info(text + ' <i class="ld ld-ring"></i>');
};
var hideProcessing = function () {
    toastr.clear(processingToastr);
};
var showFixedToastr = function (type, text) {
    toastr.options.timeOut = 0;
    toastr.options.extendedTimeOut = 0;
    switch (type) {
        case 'success':
            fixedToastr = toastr.success(text);
            break;
        case 'info':
            fixedToastr = toastr.info(text);
            break;
        case 'warning':
            fixedToastr = toastr.warning(text);
            break;
        case 'error':
            fixedToastr = toastr.error(text);
            break;
        case 'default':
            fixedToastr = toastr.info(text);
            break;
    }
};
var hideFixedToastr = function () {
    toastr.clear(fixedToastr);
};