import Guide from '../guides/Guide.js';
import Pages from '../guides/Pages.js';

let highlighted_pages;
let exercise_pages;
let nutrition_pages;
let goal_len = 0;

let set_pages = {
    highlighted_guides: highlighted_pages,
    exercise_favorites: exercise_pages,
    nutrition_favorites: nutrition_pages
};
// TODO for Joel:  Goals ID should use goal id from database instead of goal text
// TODO why is the Add a goal button added via script and not apart of the html

$( window ).on( "load", function() {
    get_guides(['highlighted', 'nutrition', 'exercise']);
    get_body();
    get_goals();
});

function get_body() {
    $.ajax({
        type: 'POST',
        url: '/pn/api/nutri/get_nutrition.php',
        success: function(data) {
            let json = JSON.parse(data);
            for(let key in json) {
                if(json.hasOwnProperty(key)) {
                    $('#blood_type').val(json[key]['blood_type']);
                    $('#body_type').val(json[key]['body_type']);
                    $('#weight').val(json[key]['current_weight']);
                    $('#activity_level').val(json[key]['activity_lvl']);
                    break;
                }
            }
        },
        error: function() {
            console.log("Get_Body ERROR");
        }
    });
}

function save_body() {
    $('#save_body_btn').prop('disabled', true);
    const blood_type = $('#blood_type').val();
    const body_type = $('#body_type').val();
    const weight = $('#weight').val();
    const activity_level = $('#activity_level').val();
    $.ajax({
        type: 'POST',
        url: '/pn/api/nutri/save_nutrition.php',
        data: {
            blood_type: blood_type,
            body_type: body_type,
            weight: weight,
            activity_level: activity_level
        },
        success: function() {
            $('#save_body_btn').prop('disabled', false);
        },
        error: function() {
            $('#add_goal_btn').prop('disabled', false);
            console.log("Get_Body ERROR");
        }
    });
}

function get_goals() {
    $.ajax({
        type:'POST',
        url: '/pn/api/dashboard/get_goals.php',
        success: function(data) {
            let goal_str = Array();
            const json = JSON.parse(data);
            for(let key in json) {
                if (json.hasOwnProperty(key))
                    goal_str.push(
                        '<li>' +
                        json[key]['goal'] +
                        '<button type="button" ' +
                        'class="delete-goal close text-right" ' +
                        'id="' + json[key]['goal'] + '"">' +
                        '×' +
                        '</button>' +
                        '</li>');
            }
            goal_str.push(
                '<li class="text-right" id="last_goal_line">' +
                '<button class="btn btn-primary" id="add_goal_btn" ' +
                'style="background-color:green;border-color:green;height:100%">' +
                'Add Goal' +
                '</button>' +
                '</li>'
            );
            $(document).on('click','#add_goal_btn', function (){
                add_goal();
            });
            $(document).on('click','.delete-goal', function (){
                console.log($(this));
                delete_goal($(this));
            });
            $('#goals').html(goal_str);
        },
        error: function() {
            console.log("Get_Goals ERROR");
        }
    });
}

function add_goal() {
    console.log('adding goal');
    $('#last_goal_line').before(
        '<li id="add_goal_line">' +
        '<div class="row">' +
        '<input id="goal_input" class="form-control col-11" type="text" placeholder="New Goal">' +
        '<button class="btn btn-primary col-1" id="submit_goal_btn"> ' +
        '<i class="fas fa-plus"> </i>' +
        '</button>' +
        '</div>' +
        '</li>'
    );
    $(document).on('click','#submit_goal_btn', function (){
        submit_goal();
    });
    $('#add_goal_btn').prop('disabled', true);
}

function submit_goal() {
    const goal = $('#goal_input').val();
    if(goal === "") {
        return;
    }
    $.ajax({
        type: 'POST',
        url: '/pn/api/dashboard/submit_goal.php',
        data: {
            goal: goal
        },
        success: function(data) {
            const parent = document.getElementById('goals');
            parent.removeChild(document.getElementById('add_goal_line'));
            $('#last_goal_line').before(
                '<li>' + data +
                '<button type="button" ' +
                'class="delete-goal close text-right" ' +
                'id="' + data + '">' +
                '×' +
                '</button>' +
                '</li>'
            );
            $(document).on('click','.delete-goal', function (){
                delete_goal($(this));
            });
            ++goal_len;
            $('#add_goal_btn').prop('disabled', false);
        },
        error: function() {
            console.log("Submit_Goal ERROR");
        }
    });
}

function delete_goal(button) {
    console.log(button);
    let goal = $(button).attr('id');
    let list = document.querySelectorAll('#goals li');
    $.ajax({
        type: 'POST',
        url: '/pn/api/dashboard/remove_goal.php',
        data: {
            goal: goal
        },
        success: function() {
            for(let key in list) {
                if(list.hasOwnProperty(key)) {
                    if(list[key].childNodes[0].textContent === goal) {
                        list[key].remove();
                        break;
                    }
                }
            }
            --goal_len;
        },
        error: function() {
            console.log("ERROR");
        }
    });
}

//TODO Kinda weird that this method has tags as a parameter ("get_guides(['highlighted', 'nutrition', 'exercise']);") but its all hardcoded in the success statement, might as well just skip it
function get_guides(tags, categories, favorites) {
    $.ajax({
        type:'POST',
        url: '/pn/api/guides/get_guides_tag_filtered.php',
        data: {
            tags: tags
        },
        success: function(data) {
            let guide;
            let nutrition = Array();
            let exercise = Array();
            let highlighted = Array();
            const json = JSON.parse(data);
            for(let key in json) {
                if(json.hasOwnProperty(key)) {
                    guide = new Guide(json[key],function(){
                        get_guides(['highlighted', 'nutrition', 'exercise']);
                    });
                    if(guide.is_favorite === 1) {
                        if(guide.tags.includes('nutrition'))
                            nutrition.push(guide);
                        if(guide.tags.includes('exercise'))
                            exercise.push(guide);
                    }
                    if(guide.tags.includes('highlighted'))
                        highlighted.push(guide);
                }
            }
            set_pages["nutrition_favorites"] = new Pages(nutrition, $("#nutrition_favorites"));
            set_pages["exercise_favorites"] = new Pages(exercise, $("#exercise_favorites"));
            set_pages["highlighted_guides"] = new Pages(highlighted, $("#highlighted_guides"));
            for(let title in set_pages) {
                if(set_pages.hasOwnProperty(title))
                    set_pages[title].update_html();
            }
        },
        error: function() {
            console.log("get_guides ERROR");
        }
    });
}