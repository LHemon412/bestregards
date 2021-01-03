$(() => {
  // Load posts
  $.ajaxSetup({cache: false});
  $.get("fetchPosts.php", data => {
    var list = JSON.parse(data);
    list.forEach((data, index) => {
      let uid = data[0];
      let timestamp = data[1];
      let post_id = data[2];
      $.getJSON(`data/${uid}/${post_id}/desc.json`, function(postData) {
        $("div.posts").append(`<div class="post" id="${uid}_${post_id}" style="order: ${index}"></div>`);
        $(`div.post#${uid}_${post_id}`).append(`<div class="post_heading"></div>`);
        $(`div.post#${uid}_${post_id} .post_heading`).append(`<div class="post_author"><h3>Notebook User #${uid}</h3></div>`);
        $(`div.post#${uid}_${post_id} .post_heading`).append(`<div class="post_timestamp"><h4>${timestamp}</h4></div>`);
        $(`div.post#${uid}_${post_id}`).append(`<div class="post_content"></div>`);
        $(`div.post#${uid}_${post_id} .post_content`).append(`<div class="post_caption"><p>${postData.caption}</p></div>`);
        $(`div.post#${uid}_${post_id} .post_content`).append(`<div class="post_attachments"></div>`);
        const IMAGE_EXT = ["jpg", "jpeg", "png"];
        let main_img = "none";
        let load_atms = [];
        postData.attachments.forEach((atm, i) => {
          let extension = atm.split(".")[atm.split(".").length-1];
          if (IMAGE_EXT.includes(extension)) {
            if (main_img == "none") {
              load_atms = [atm, ...load_atms];
              main_img = atm;
            } else {
              load_atms.push(atm);
            }
          } else {
            load_atms.push(atm);
          }
        });

        console.log(load_atms);

        if (main_img == "none") {
          $(`div.post#${uid}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_others_container"></div>`)
          postData.attachments.forEach((atm, i) => {
            $(`div.post#${uid}_${post_id} .post_content .post_attachments .atmt_others_container`).append(`<div class="atmt_others" style="order: ${i};"><a href="data/${uid}/${post_id}/${atm}">${atm}</a></div>`);
          });
        } else {
          let img = new Image();
          img.onload = function() {
            let height = this.height;
            let width = this.width;
            let hwratio = height / width;
            if (this.height > ($(window).height() * 0.6)) {
              height = $(window).height() * 0.6;
              width = height / hwratio;
              let compress_ratio = ($(window).height() * 0.6) / this.height;
              if ((this.width * compress_ratio) > ($(window).width() * 0.44)) {
                width = $(window).width() * 0.44;
              }
            } else if (this.width > ($(window).width() * 0.44)){
              width = $(window).width() * 0.44;
              height = width * hwratio;
            } else {
              width /= $(window).width();
              height /= $(window).height();
            }

            let vmin = ($(window).height() > $(window).width()) ? $(window).width() * 0.01 : $(window).height() * 0.01;
            let mwidth = width + 2*vmin;
            let mheight = height + 2*vmin;
            $(`div.post#${uid}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_img_container" style="height: ${mheight}px; width: ${mwidth}px"><div class="atmt_img_preview"><img style="height: ${height}px; width=${width}px;" src="data/${uid}/${post_id}/${main_img}"></div></div>`);
            $("div.atmt_img_container").click(function() {
              $(".modal").css("display", "block");
              $(".modal img").attr("src", `data/${uid}/${post_id}/${main_img}`)
            });
            $("div.atmt_img_container").hover(function() {
              $(".atmt_img_preview img").animate({opacity: "70%"}, 40);
            }, function() {
              $(".atmt_img_preview img").animate({opacity: "100%"}, 40);
            });

            postData.attachments.forEach((atm, i) => {
              if (atm == main_img) {
                return;
              }

              let extension = atm.split(".")[atm.split(".").length-1];
              if (IMAGE_EXT.includes(extension)) {
                console.log(atm);
                if ($(".atmt_img_more").length == 0) {
                  $(".atmt_img_preview").append(`<span class="atmt_img_more">...more</span>`);
                }
              } else {
                if ($(`div.post#${uid}_${post_id} .post_content .post_attachments .atmt_others_container`).length == 0) {
                  $(`div.post#${uid}_${post_id} .post_content .post_attachments`).append(`<div class="atmt_others_container" style="width: ${$(window).width()*0.88 - mwidth - 2*vmin}px;"></div>`);
                }
                $(`div.post#${uid}_${post_id} .post_content .post_attachments .atmt_others_container`).append(`<div class="atmt_others" style="order: ${i};"><a href="data/${uid}/${post_id}/${atm}">${atm}</a></div>`);
              }
            });
          };
          img.src=`data/${uid}/${post_id}/${main_img}`;
        }
      });
    });
  });
});
