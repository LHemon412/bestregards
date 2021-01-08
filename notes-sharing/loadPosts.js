loadingPost = false;
loadingCount = 0;
loadingTarget = 0;

function addPostDisplay(data, order) {
  let username = data[0];
  let timestamp = data[1];
  let post_id = data[2];
  $.getJSON(`data/${username}/${post_id}/desc.json`, function(postData) {
    $("div.posts").append(`<div class="post" id="${username}_${post_id}" style="order: ${order}"></div>`);
    $(`div.post#${username}_${post_id}`).append(`<div class="post_heading"></div>`);
    $(`div.post#${username}_${post_id} .post_heading`).append(`<div class="post_author"><h3>${username}</h3></div>`);
    $(`div.post#${username}_${post_id} .post_heading`).append(`<div class="post_timestamp"><h4>${timestamp}</h4></div>`);
    $(`div.post#${username}_${post_id}`).append(`<div class="post_content"></div>`);
    $(`div.post#${username}_${post_id} .post_content`).append(`<div class="post_caption"><p>${postData.caption}</p></div>`);
    $(`div.post#${username}_${post_id} .post_content`).append(`<div class="post_attachments"></div>`);
    const IMAGE_EXT = ["jpg", "jpeg", "png"];
    let main_img = "none";
    let load_atms = [];
    let other_img = [];
    postData.attachments.forEach((atm, i) => {
      let extension = atm.split(".")[atm.split(".").length-1];
      if (IMAGE_EXT.includes(extension)) {
        if (main_img == "none") {
          load_atms = [atm, ...load_atms];
          main_img = atm;
        } else {
          other_img.push(atm);
          load_atms.push(atm);
        }
      } else {
        load_atms.push(atm);
      }
    });

    if (main_img == "none") {
      $(`div.post#${username}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_others_container"></div>`)
      postData.attachments.forEach((atm, i) => {
        $(`div.post#${username}_${post_id} .post_content .post_attachments .atmt_others_container`).append(`<div class="atmt_others" style="order: ${i};"><a href="data/${username}/${post_id}/${atm}">${atm}</a></div>`);
      });
      loadingCount++;
      if (loadingCount == loadingTarget) {
        finishLoadPosts();
      }
    } else {
      let img = new Image();
      img.onload = function() {
        $(`div.post#${username}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_img_container"><img src="data/${username}/${post_id}/${main_img}"></div>`);
        let $container = $(`div.post#${username}_${post_id} .atmt_img_container`);
        let $img = $(`div.post#${username}_${post_id} .atmt_img_container img`);
        $container.click(function() {
          $(".modal-image-preview-container img").attr("src", `data/${username}/${post_id}/${main_img}`);
          $(".modal-image-preview-container a").attr("href", `data/${username}/${post_id}/${main_img}`);
          $(".modal-image-preview-container img").attr("name", main_img);
          let img_list = JSON.parse($container.attr("list"));
          $(".modal-image-preview").attr("list", $container.attr("list"));
          $(".modal-image-next").css("opacity", 0.2);
          $(".modal-image-previous").css("opacity", 0.2);
          if (img_list.length > 1) {
            $(".modal-image-next").css("opacity", 1);
            $(".modal-image-next").click(function() {
              if ($(this).css("opacity") == 0.2) {
                return;
              }
              let img_index = img_list.indexOf($(".modal-image-preview-container img").attr("name"));
              let next_img_name = img_list[img_index+1];
              $(".modal-image-preview-container img").fadeOut(200, function() {
                $(".modal-image-preview-container img").attr("src", `data/${username}/${post_id}/${next_img_name}`);
                $(".modal-image-preview-container a").attr("href", `data/${username}/${post_id}/${next_img_name}`);
                $(".modal-image-preview-container img").attr("name", next_img_name);
                $(".modal-image-preview-container img").fadeIn(200);
              });
              $(".modal-image-previous").animate({opacity: 1}, 200);
              if ((img_index+2) == img_list.length) {
                $(".modal-image-next").animate({opacity: 0.2}, 200);
              }
            });
            $(".modal-image-previous").click(function() {
              if ($(this).css("opacity") == 0.2) {
                return;
              }
              let img_index = img_list.indexOf($(".modal-image-preview-container img").attr("name"));
              let prev_img_name = img_list[img_index-1];
              $(".modal-image-preview-container img").fadeOut(200, function() {
                $(".modal-image-preview-container img").attr("src", `data/${username}/${post_id}/${prev_img_name}`);
                $(".modal-image-preview-container a").attr("href", `data/${username}/${post_id}/${prev_img_name}`);
                $(".modal-image-preview-container img").attr("name", prev_img_name);
                $(".modal-image-preview-container img").fadeIn(200);
              });
              $(".modal-image-next").animate({opacity: 1}, 200);
              if ((img_index-1) == 0) {
                $(".modal-image-previous").animate({opacity: 0.2}, 200);
              }
            });
          }
          $(".modal").toggle("fade", 300);
          $(".modal-image-preview").toggle("slide", {direction: "down"}, 300);
        });
        $img.hover(function() {
          $(this).animate({opacity: "80%"}, 50);
          $container.addClass("active");
        }, function() {
          $(this).animate({opacity: "100%"}, 50);
          $container.removeClass("active");
        });

        $container.attr("list", JSON.stringify([main_img, ...other_img]));

        postData.attachments.forEach((atm, i) => {
          if (atm == main_img) {
            return;
          }

          let extension = atm.split(".")[atm.split(".").length-1];
          if (IMAGE_EXT.includes(extension)) {
            if ($(".atmt_img_more").length == 0) {
              $container.append(`<span class="atmt_img_more">...more</span>`);
            }
          } else {
            if ($(`div.post#${username}_${post_id} .post_content .post_attachments .atmt_others_container`).length == 0) {
              $(`div.post#${username}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_others_container"></div>`);
            }
            $(`div.post#${username}_${post_id} .post_content .post_attachments .atmt_others_container`).append(`<div class="atmt_others" style="order: ${i};"><a href="data/${username}/${post_id}/${atm}">${atm}</a></div>`);
          }
        });
        loadingCount++;
        if (loadingCount == loadingTarget) {
          finishLoadPosts();
        }
      };
      img.src=`data/${username}/${post_id}/${main_img}`;
    }
  });
}

function loadPosts(omitno=0) {
  if (loadingPost) {
    return;
  }
  loadingPost = true;
  $(".loader-icon").toggle("fade", "100");
  $.get({
    url: "fetchPosts.php",
    data: {type: "data", omit: omitno},
    cache: false,
    success: function(list) {
      loadingTarget = list.length;
      list.forEach((data, index) => {
        addPostDisplay(data, index + omitno)
      });
    }
  });
}

function finishLoadPosts() {
  loadingCount = 0;
  if ($(".posts").css("display") == "none") {
    $(".posts").toggle("slide", {direction: "up"}, 1000);
  }
  $(".loader-icon").toggle("fade", "100");
  setTimeout(function() {
    loadingPost = false;
  }, 500);
}

$(document).ready(() => {
  $("body").css("background-color", "#242526");
  $(".loader-icon").hide();
  $(".new-posts-alert").hide();

  $(".new-posts-alert button").on({
    click: function() {
      $alert = $(".new-posts-alert")
      $alert.addClass("new-posts-alert-active").animate({opacity: 0}, 200);
      setTimeout(function() {
        $(".posts").toggle("slide", {direction: "up"}, 1000);
        $alert.removeClass("new-posts-alert-active").css("display", "none");
        setTimeout(function() {
          $(".posts>*").remove();
          loadPosts();
        }, 1000);
      }, 200);
    }
  });

  loadPosts();

  $(window).scroll(function() {
    if ($(document).height() == $(this).height() + $(this).scrollTop()) {
      if (!loadingPost) {
        let loaded_posts = $(".posts>*").length;
        $.get("fetchPosts.php", {type: "number"}, function(data) {
          if (data.count > loaded_posts) {
            loadPosts(loaded_posts);
          }
        })
      }
    }
  });

  setInterval(function() {
    if ($(".posts>div").length == 0) { return; }

    let latest_item, username, post_id
    latest_item = $(".posts>div").filter(function(i){return $($(".posts>div")[i]).css("order")==0});
    username = parseInt($(latest_item).attr("id").replace(new RegExp(".[0-9]+$"), ""));
    post_id = parseInt($(latest_item).attr("id").replace(new RegExp("^[0-9]+."), ""));
    $.get("fetchPosts.php", {
      type: "newposts_number",
      username: username,
      post_id: post_id
    }, function(data) {
      if (data.count > 0) {
        $alert = $(".new-posts-alert");
        if ($alert.css("display") == "none") {
          $alert.css("display", "flex").css("opacity", 0).animate({opacity: 1}, 170);
        }
      }
    })
  }, 30000); // Every 30s
});
