jQuery(document).ready(function ($) {
  let tlDataId = $(".bp_titleline");

  // console.log(tlDataId);
  // tlDataId.map(function (index, item) {
  //   console.log(item);
  // });

  // console.log(timelineData);
  tlDataId.map((index, timeline_item) => {
    const timelineData = $(timeline_item).data("timeline");
    timeline_item.removeAttribute("data-timeline");

    if (!timelineData) return false;

    const { timeline_type, date_location, item_datas, start_item, move_item, visible_items, vertica_trigger, rtl_mode, content_position = "" } = timelineData;

    $(timelineData).removeAttr("data-timeline");
    // console.log({ timeline_type })
    $(timeline_item).addClass(`content_${timeline_type}_${content_position}`);

    $(timeline_item).timeline({
      mode: timeline_type || "vertical",
      horizontalStartPosition: date_location,
      forceVerticalMode: 600,
      verticalStartPosition: date_location,
      verticalTrigger: `${vertica_trigger}%`,
      moveItems: parseInt(move_item),
      startIndex: parseInt(start_item),
      visibleItems: parseInt(visible_items),
      rtlMode: rtl_mode === "1",
    });

    // const maintainContentPosition = (el, mode, position, labelLocation) => {
    //   const itemsContainer = el.querySelector(`.timeline__items`);
    //   const allTimelineItem = el.querySelectorAll(`.timeline__items .timeline__item`);
    //   const allTimelineNavButton = el.querySelectorAll(`.timeline-nav-button`);
    //   const timelineDivider = el.querySelector(`.timeline-divider`);

    //   allTimelineItem?.forEach((item, index) => {

    //     if (mode === "horizontal") {

    //       if (position === "end") {
    //         if (item.classList.contains('timeline__item--top')) {
    //           item.classList.remove('timeline__item--top');
    //           item.classList.add('timeline__item--bottom');
    //         }
    //         item.style.transform = "translateY(20px)";
    //         allTimelineNavButton.forEach(item => {
    //           item.style.top = "20px";
    //         });
    //         timelineDivider.style.top = "20px";
    //         itemsContainer.style.height = `${item.getBoundingClientRect().height + 20}px`;
    //       } else if (position === "start") {
    //         if (item.classList.contains('timeline__item--bottom')) {
    //           item.classList.remove('timeline__item--bottom');
    //           item.classList.add('timeline__item--top');
    //         }
    //         item.style.transform = "unset";
    //         allTimelineNavButton.forEach(btn => {
    //           btn.style.top = item.clientHeight;
    //         });
    //         timelineDivider.style.top = item.clientHeight;
    //         itemsContainer.style.height = `${item.getBoundingClientRect().height + 20}px`;
    //       }
    //     } else {
    // 			if (labelLocation === "top") {
    // 				if (index % 2 === 0) {
    // 					item.classList.remove('timeline__item--bottom');
    // 					item.classList.add('timeline__item--top');
    // 				} else {
    // 					item.classList.remove('timeline__item--top');
    // 					item.classList.add('timeline__item--bottom');
    // 					item.style.transform = item.clientHeight;
    // 				}

    // 			} else if (labelLocation === "bottom") {
    // 				if (index % 2 === 0) {
    // 					item.classList.remove('timeline__item--top');
    // 					item.classList.add('timeline__item--bottom');
    // 					item.style.transform = item.clientHeight;
    // 				} else {
    // 					item.classList.remove('timeline__item--bottom');
    // 					item.classList.add('timeline__item--top');
    // 				}
    // 			}
    // 			// Get all elements with class "timeline__item--bottom" and find the highest one
    // 			const bottomItems = el.querySelectorAll(`.timeline__item--bottom`);
    //       console.log(bottomItems, "Wildflower");
    // 			bottomItems.forEach(item => {
    // 				item.style.height = "fit-content";
    // 			});
    // 			const bottomItemHighest = Math.max(...Array.from(bottomItems).map(el => el.getBoundingClientRect().height));
    // 			const topItems = el.querySelectorAll(`.timeline__item--top`);

    // 			const topItemsHighest = Math.max(...Array.from(topItems).map(el => el.getBoundingClientRect().height));
    // 			itemsContainer.style.height = `${bottomItemHighest + topItemsHighest}px`;

    // 		}


    //   });
    // }

    const maintainContentPosition = (el, mode, position, labelLocation) => {
      const itemsContainer = el.querySelector(`.timeline__items`);
      const allTimelineItem = el.querySelectorAll(`.timeline__items .timeline__item`);
      const allTimelineNavButton = el.querySelectorAll(`.timeline-nav-button`);
      const timelineDivider = el.querySelector(`.timeline-divider`);

      allTimelineItem?.forEach((item, index) => {
        if (mode === "horizontal") {
          if (position === "end") {
            if (item.classList.contains("timeline__item--top")) {
              item.classList.remove("timeline__item--top");
              item.classList.add("timeline__item--bottom");
            }
            item.style.transform = "translateY(20px)";
            allTimelineNavButton.forEach((btn) => {
              btn.style.top = "20px";
            });
            timelineDivider.style.top = "20px";
            itemsContainer.style.height = `${item.getBoundingClientRect().height + 20}px`;
          } else if (position === "start") {
            if (item.classList.contains("timeline__item--bottom")) {
              item.classList.remove("timeline__item--bottom");
              item.classList.add("timeline__item--top");
            }
            item.style.transform = "unset";
            allTimelineNavButton.forEach((btn) => {
              btn.style.top = item.clientHeight;
            });
            timelineDivider.style.top = item.clientHeight;
            itemsContainer.style.height = `${item.getBoundingClientRect().height + 20}px`;
          } else {
            if (labelLocation == "top") {
              if (index % 2 == 0) {
                item.classList.remove("timeline__item--bottom");
                item.classList.add("timeline__item--top");
              } else {
                item.classList.remove("timeline__item--top");
                item.classList.add("timeline__item--bottom");
                item.style.transform = `${item.clientHeight}px`;
              }
              // Fix: Ensure rendering is complete before measuring heights
              setTimeout(() => {
                const bottomItems = el.querySelectorAll(".timeline__item--bottom");
                bottomItems.forEach((item) => {
                  item.style.height = "fit-content";
                });
                const bottomItemHighest = Math.max(
                  ...Array.from(bottomItems).map((el) => el.getBoundingClientRect().height)
                );

                const topItems = el.querySelectorAll(".timeline__item--top");
                const topItemsHighest = Math.max(
                  ...Array.from(topItems).map((el) => el.getBoundingClientRect().height)
                );

                itemsContainer.style.height = `${bottomItemHighest + topItemsHighest + 20}px`;

              }, 50);

            } else if (labelLocation == "bottom") {
              if (index % 2 === 0) {
                item.classList.remove("timeline__item--top");
                item.classList.add("timeline__item--bottom");
                item.style.transform = `${item.clientHeight}px`;
              } else {
                item.classList.remove("timeline__item--bottom");
                item.classList.add("timeline__item--top");
              }

              // Fix: Ensure rendering is complete before measuring heights
              setTimeout(() => {
                const topItems = el.querySelectorAll(".timeline__item--top");
                topItems.forEach((item) => {
                  item.style.height = "fit-content";
                });
                const topItemsHighest = Math.max(
                  ...Array.from(topItems).map((el) => el.getBoundingClientRect().height)
                );
                topItems.forEach((item) => {
                  item.style.height = `${topItemsHighest}px`;
                });

                timelineDivider.style.top = `${topItemsHighest}px`;
                allTimelineNavButton.forEach((btn) => {
                  btn.style.top = `${topItemsHighest}px`;
                });

                const bottomItems = el.querySelectorAll(".timeline__item--bottom");
                bottomItems.forEach((item) => {
                  item.style.transform = `translateY(${topItemsHighest}px)`;
                });
                const bottomItemHighest = Math.max(
                  ...Array.from(bottomItems).map((el) => el.getBoundingClientRect().height)
                );


                itemsContainer.style.height = `${bottomItemHighest + topItemsHighest}px`;

              }, 50);
            }

          }
        }

        if (mode === "vertical") {
          if (position == "start") {
            const rightItems = el.querySelectorAll(".timeline__item--right");

            rightItems.forEach((item) => {
              item.classList.remove("timeline__item--right");
              item.classList.add("timeline__item--left");
            });
          }
          else if (position == "end") {
            const leftItems = el.querySelectorAll(".timeline__item--left");

            leftItems.forEach((item) => {
              item.classList.remove("timeline__item--left");
              item.classList.add("timeline__item--right");
            });
          }
        }
      });
    };

    maintainContentPosition(timeline_item, timeline_type, content_position, date_location);
  });
});

