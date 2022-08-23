function doValidate() {
  console.log('Validating...');
  try {
    email = document.getElementById('email').value;
    pass = document.getElementById('pass').value;
    console.log('email: ' + email
                  + '\npass: ' + pass);
    if (pass == null || pass == "" || email == null || email == "") {
      alert("Both fields must be filled out");
      return false;
    }
    return true;
  } catch(e) {
      return false;
  }
}

function get_position(profile_id) {
  let positions;
  $.ajaxSetup({async:false});
  $.getJSON("get_position.php", {profile_id: profile_id}, data => positions = data);
  return positions;
}

function get_education(profile_id) {
  let education;
  $.ajaxSetup({async:false});
  $.getJSON("get_education.php", {profile_id: profile_id}, data => education = data);
  return education;
}

function print_positions(positions) {
  $('#positions').append('<p>Positions:</p><ul>');
  $(positions.forEach(element => $('#positions').append(`<li>${element.year}: ${element.description}</li>`)));
  $('#positions').append('</ul>');
} 

function print_education(education) {
  $('#educations').append('<p>Education:</p><ul>');
  $(education.forEach(element => $('#educations').append(`<li>${element.year}: ${element.institution_name}</li>`)));
  $('#educations').append('</ul>');
} 

function delPos(table_name, pos) {
  $(`#${table_name}_${pos}`).remove();
  
  if (table_name == 'education') {
    let index = education_arr.indexOf(pos);
    if (index > -1) education_arr.splice(index, 1); 

  } else if (table_name == 'position') {
    let index = position_arr.indexOf(pos);
    if (index > -1) position_arr.splice(index, 1); 
  }
}

function doTables(table_arr, table_name) {
  $(`#add_${table_name}`).click(event => {
    event.preventDefault();

    if (table_arr.length > 8) {
      alert('You cannot have more than 9 entries');
      return;
    } 

    createDivs(table_arr, table_name);
  })
}

function getURL(param) {
  let searchParams = new URLSearchParams(window.location.search);
  return searchParams.get(param);
}

function createDivs(  table_arr, table_name, 
                      position_data={year:'', description:''},
                      education_data={year:'', institution_name:''} ) {

  let pos = (table_arr.length == 0) ? 1 : table_arr.at(-1) + 1; 
  
  let first_input, second_input;
  if (table_name == 'position') {
    first_input = `<input type="text" class="form-control" placeholder="Year" 
                          name="position_year_${pos}" value="${position_data.year}" />`;
    second_input = `<textarea class="form-control" placeholder="Description" 
                              name="description_${pos}">${position_data.description}</textarea>`;
  } else {
    first_input = `<input type="text" class="form-control" placeholder="Year" 
                          name="education_year_${pos}" value="${education_data.year}" />`;
    second_input = `<input  type="text" class="institution form-control" placeholder="Institution" 
                            autocomplete="off" name="institution_${pos}" value="${education_data.institution_name}" />`;
  }              

  $(`#${table_name}`).append(` 
    <div id="${table_name}_${pos}">
      <div class="input-group mb-2 user-input">
        ${first_input}
        <input type="button" onClick="delPos('${table_name}', ${pos}); return false;"  class="btn-sm btn btn-light" value='-' />
      </div>
      <div class="input-group mb-2 user-input">${second_input}</div>
    </div>
  `);

  $( ".institution" ).autocomplete({
    source: "get_institution.php",
    appendTo: `#${table_name}_${pos}`
  });

  table_arr.push(pos);
}
