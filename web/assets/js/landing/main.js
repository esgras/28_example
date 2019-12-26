let editClass = '.edit-data';
$(editClass).attr('contenteditable', true);
$(editClass).on('click', function(e) {
    e.stopImmediatePropagation();
});

$('a').click(function(e) {
    e.preventDefault();
    return false;
});
let editElement, oldUrl;
$('img').click( e => {
    e.stopPropagation();
    e.preventDefault();

    if (editElement) {
        cancelForm();
    }

    editElement = e.target;
    modal.show();
    setFormImage(e.target.src);
});
let remove = false;
$('#modalRemove').on('click', function(e) {
    editElement.hidden = true;
    remove = true;
});

function clearImageInput() {
    setFormImage('');
}

function setFormImage(src) {
    $('#modalSrc').val(src);
}

$('#modalButton').click(function(e) {
    let url = $('#modalSrc').val().trim();
    if (!oldUrl) {
        oldUrl = editElement.src;
    }
    editElement.src = url;
});

$('#modalSave').on('click', function(e) {
    if (remove) {
        editElement.remove();
    }
    remove = oldUrl = false;
    modal.hide();
    clearImageInput();
});
$('#modalCancel').on('click',cancelForm);
$('body').on('click', function(e) {
    if (!e.target.closest('#modal')) {
        console.log('cancel form');
        cancelForm();
    }
});

function cancelForm() {
    if (remove) {
        editElement.hidden = false;
    }
    if (oldUrl) {
        editElement.src = oldUrl;
    }
    remove = oldUrl = false;
    modal.hide();
    clearImageInput();
}

let hiddenClass = 'hidden';

let modal = document.querySelector('#modal');
modal.show = function() {
    this.classList.remove(hiddenClass);
};
modal.hide = function() {
    this.classList.add(hiddenClass);
};
$(modal).on('submit', () => false);
modal.hide();

function hideElement(el) {
    let style = el.style.cssText;
    let hiddenStr = ' display: none !important';

    if (el.style.cssText.indexOf('display') !== -1) {
        el.style.cssText = el.style.cssText.replace(/display:(.+)?;/, hiddenStr);
    } else {
        el.style.cssText += hiddenStr;
    }
}

function showElement(el) {
    let showStr = 'display: block !important;';
    if (el.style.cssText.indexOf('display') !== -1) {
        el.style.cssText = el.style.cssText.replace(/display:(.+)?;/, showStr);
    } else {
        el.style.cssText += showStr;
    }
}


let modalBlockElement, modalBlockRemove;
let modalBlock = document.querySelector('#modalBlock');
modalBlock.show = function() {
    this.classList.remove(hiddenClass);
};
modalBlock.hide = function() {
    this.classList.add(hiddenClass);
};

modalBlock.cancel = function() {
    showElement(modalBlockElement);
    modalBlockElement = null;
    modalBlock.hide();
};


$('#modalBlockRemove').on('click', function(e) {
    hideElement(modalBlockElement);
    modalBlockRemove = true;
});

$('#modalBlockCancel').on('click', function(e) {
    showElement(modalBlockElement);
    modalBlockRemove = null;
    modalBlockElement = null;
    modalBlock.hide();
});

$('#modalBlockSave').on('click', function(e) {
    if (modalBlockRemove) {
        modalBlockElement.remove();
        modalBlockRemove = null;
    }
    modalBlockElement = null;
    modalBlock.hide();
});

$('.edit-block').on('click', function(e) {
    cancelForm();
    modalBlockRemove = false;
    modalBlockElement = e.target;
    modalBlock.hide();
    modalBlock.show();
});


document.body.addEventListener('keydown', function(e) {

    if (e.keyCode == 80 && e.ctrlKey) {
        e.preventDefault();
        $(editClass).removeAttr('contenteditable');
        let $body = $('body').clone(true);
        $body.find('script').remove();


        text = '<!DOCTYPE html><head>'+$('head').html()+'</head>'+'<body>'+$body.html()+'</body>';
        $.ajax(
            {
                method: 'POST',
                data: {'html': text},
                url: ajaxUrl
            }
        );
    }
});