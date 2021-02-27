
// Update Message User
function update_list_user_chat(data){
    let template = '<ul class="nav nav-pills flex-column">';
      data.user_with_new_message.forEach((d,i) => {
        template += `
              <li class="nav-item active">
                <a href="#" data-user_id="${d.user_id}" data-userid="${d.userid}" class="nav-link engineer_list">
                  <i class="fas fa-user"></i> ${d.user_name}
                  `;
                  if(d.unread_count > 0){
                    template += `<span class="badge bg-primary float-right">${d.unread_count}</span>`
                  }
                  template += `
                </a>
              </li>`
      });
      data.user_with_no_message.forEach((d,i) => {
        template += `
              <li class="nav-item active">
                <a href="#" data-user_id="${d.id}" data-userid="${d.userid}" class="nav-link engineer_list">
                  <i class="fas fa-user"></i> ${d.name}
                </a>
              </li>
              `
      });
      template += `</ul>`;
    return template;
}