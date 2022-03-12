// this variable is the list in the dom, it's initiliazed when the document is ready
var $collectionHolder1;
// the link which we click on to add new items
var $addNewItem1 =$('<a href="#" class="button ripple-effect big margin-top-30"><i class="icon-feather-plus"></i>Experience </a>');// when the page is loaded and ready
$(document).ready(function () {
    // get the collectionHolder, initilize the var by getting the list;
    $collectionHolder1 = $('#experiance_list');
    // append the add new item link to the collectionHolder
    $collectionHolder1.append($addNewItem1);
    // add an index property to the collectionHolder which helps track the count of forms we have in the list
    $collectionHolder1.data('index', $collectionHolder1.find('.panel').length)
    // finds all the panels in the list and foreach one of them we add a remove button to it
    // add remove button to existing items
    $collectionHolder1.find('.panel').each(function () {
        // $(this) means the current panel that we are at
        // which means we pass the panel to the addRemoveButton function
        // inside the function we create a footer and remove link and append them to the panel
        // more informations in the function inside
        addRemoveButton1($(this));
    });
    // handle the click event for addNewItem
    $addNewItem1.click(function (e) {
        // preventDefault() is your  homework if you don't know what it is
        // also look up preventPropagation both are usefull
        e.preventDefault();
        // create a new form and append it to the collectionHolder
        // and by form we mean a new panel which contains the form
        addNewForm1();
    })
});
/*
 * creates a new form and appends it to the collectionHolder
 */
function addNewForm1() {
    // getting the prototype
    // the prototype is the form itself, plain html
    var prototype1 = $collectionHolder1.data('prototype');
    // get the index
    // this is the index we set when the document was ready, look above for more info
    var index1 = $collectionHolder1.data('index');
    // create the form
    var newForm1 = prototype1;
    // replace the __name__ string in the html using a regular expression with the index value
    newForm1 = newForm1.replace(/__name__/g, index1);
    // incrementing the index data and setting it again to the collectionHolder
    $collectionHolder1.data('index', index1+1);
    // create the panel
    // this is the panel that will be appending to the collectionHolder
    var $panel = $('<div class="panel panel-warning"><div class="panel-heading"></div></div>');
    // create the panel-body and append the form to it
    var $panel1Body = $('<div class="panel-body"></div>').append(newForm1);
    // append the body to the panel
    $panel.append($panel1Body);
    // append the removebutton to the new panel
    addRemoveButton1($panel);
    // append the panel to the addNewItem
    // we are doing it this way to that the link is always at the bottom of the collectionHolder
    $addNewItem1.before($panel);
}

/**
 * adds a remove button to the panel that is passed in the parameter
 * @param $panel
 */
function addRemoveButton1 ($panel) {
    // create remove button
    var $removeButton1 = $('<a href="#" class="btn btn-danger">Remove</a>');
    // appending the removebutton to the panel footer
    var $panelFooter1 = $('<div class="panel-footer"></div>').append($removeButton1);
    // handle the click event of the remove button
    $removeButton1.click(function (e) {
        e.preventDefault();
        // gets the parent of the button that we clicked on "the panel" and animates it
        // after the animation is done the element (the panel) is removed from the html
        $(e.target).parents('.panel').slideUp(1000, function () {
            $(this).remove();
        })
    });
    // append the footer to the panel
    $panel.append($panelFooter1);
}