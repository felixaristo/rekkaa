<head>
    <style>
        .text-center {
            text-align: center;
        }

        .timeline-item {
            cursor: pointer;
            transition: background-color 0.3s; /* Add a smooth transition effect */

            /* Additional styles for the default state (before hover) */
            /* Customize these styles to your preference */
            background-color: transparent;
        }

        .timeline-item:hover {
            /* Additional styles for the hover state */
            /* Customize these styles to your preference */
            background-color: transparent; /* Change to the desired background color on hover */
        }

        .timeline-item:hover h6 {
            color: orange; /* Change the color of h6 (item.announcement_title) to white on hover */
        }

        .timeline-item:hover .text-muted {
            color: orange; /* Change the color of .text-muted (item.announcement_view_description) to white on hover */
        }

        #modalContent {
            white-space: pre-line; /* Handle line breaks within these elements */
        }

        .accordion-button:hover {
            color: orange
        }

        .accordion-button {
            font-size: small;
        }

    </style>
</head>
<div class="row">
    <div class="col-lg-12 mb-0 order-0">
        @include('karyawan.kehadiran.absensi')
    </div>
    <div class="col-lg-12 mb-3 order-0">
        <div class="card">
            <div class="card-body body-pengumuman">  
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mb-4">Pengumuman</h5>
                    <button type="button" id="deleteAllAnnounce" style="display: none;" class="btn btn-sm btn-secondary custom-tutup" data-dismiss="modal">
                        <span class="bx bx-trash"></span> Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mb-3 order-0">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-start justify-content-center">
                    <h5 class="mb-4">Sisa Cuti</h5>
                </div>
                <div class="d-none d-flex justify-content-center d-md-flex" id="sisa-cuti-container-desktop">
                    <!-- Desktop container for squares -->
                </div>
                <div class="d-md-none" id="sisa-cuti-container-mobile">
                    <!-- Mobile container for squares -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal centered fade" id="timelineModal" data-bs-focus="false" tabindex="-1" role="dialog" aria-labelledby="timelineModalLabel" aria-hidden="true" data-backdrop="static">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="timelineModalLabel">Timeline Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div id="modalContent">
        <!-- Content of the clicked timeline item will be displayed here -->
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary custom-tutup" id="btn-delete" data-dismiss="modal" data-announcement-id="">
            <span class="bx bx-trash"></span> Hapus
        </button>
    </div>
    </div>
</div>
</div>

<script>
    $(function() {      
        let unread = 0;
        $.ajax({
            url: "/karyawan/cuti/sisa-cuti",
            method: "GET",
            success: function(data) {
                // Check if there is data to display
                if (data && data.data && data.data.length > 0) {
                    // Loop through the data.data and create squares
                    data.data.forEach(function(item, index) {
                        // Create a div for base_description
                        var baseDescriptionMobile = $("<div>")
                            .addClass("base-description mb-2")
                            .css({
                                fontStyle: "italic" // Set the text to italic
                            })
                            .text(item.base_description);

                        var baseDescriptionDesktop = $("<div>")
                            .addClass("base-description mb-2")
                            .css({
                                fontStyle: "italic" // Set the text to italic
                            })
                            .text(item.base_description);

                        // Create a square div
                        var squareMobile = $("<div>")
                            .addClass("square rounded")
                            .css({
                                width: "100px",
                                height: "100px",
                                background: "orange", // Change the background color to orange
                                color: "white",
                                textAlign: "center",
                                display: "flex",
                                flexDirection: "column",
                                justifyContent: "center"
                            });

                        var squareDesktop = $("<div>")
                            .addClass("square rounded")
                            .css({
                                width: "100px",
                                height: "100px",
                                background: "orange", // Change the background color to orange
                                color: "white",
                                textAlign: "center",
                                display: "flex",
                                flexDirection: "column",
                                justifyContent: "center"
                            });

                        // Create a div for quota with a bigger font size
                        var quotaMobile = $("<div>")
                            .addClass("quota")
                            .css({
                                fontSize: "24px", // Set a larger font size
                            })
                            .text(item.quota);

                        var quotaDesktop = $("<div>")
                            .addClass("quota")
                            .css({
                                fontSize: "24px", // Set a larger font size
                            })
                            .text(item.quota);

                        // Append divs to the square
                        squareMobile.append(quotaMobile);
                        squareDesktop.append(quotaDesktop);

                        var pairContainerDesktop = $("<div>")
                            .addClass("pair-container d-md-flex")
                            .css({
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                marginRight: "20px" // Add right margin to each pair except the last one for desktop
                            });

                        pairContainerDesktop.append(baseDescriptionDesktop, squareDesktop);
                        $("#sisa-cuti-container-desktop").append(pairContainerDesktop);

                        var pairContainerMobile = $("<div>")
                            .addClass("pair-container")
                            .css({
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                marginBottom: "20px" // Add bottom margin to each pair for mobile
                            });

                        pairContainerMobile.append(baseDescriptionMobile, squareMobile);
                        $("#sisa-cuti-container-mobile").append(pairContainerMobile);
                    });


                } else {
                    // Display a message if there is no data
                    $("#sisa-cuti-container-desktop, #sisa-cuti-container-mobile").html("<p>Anda tidak mendapatkan cuti apapun</p>");
                }
            },
            error: function() {
                // Handle errors if the request fails
                $("#sisa-cuti-container").html("<p>Error loading data.</p>");
            }
        });

        function loadTimelineData() {
            $(".spinner-box").css({ display: "table" });
            $.ajax({
            url: "/karyawan/pengumuman/list-pengumuman",
            method: "GET",
            success: function(data) {
                $(".body-pengumuman .timeline").empty();

                if (data.data.length > 0) {
                    $(".body-pengumuman p").remove();
                    $(".custom-tutup").css("display", "none");

                    // Create a timeline
                    var timeline = $("<ul>").addClass("timeline");
                    var totalUnread = data.unread;
                    unread = totalUnread;

                    // Loop through data.data and create timeline items
                    $.each(data.data, function(index, item) {
                        var timelineItem = $("<li>")
                            .addClass("timeline-item timeline-item-transparent")
                            .attr("data-title", item.announcement_title)
                            .attr("data-stdate", moment(item.announcement_start_date).format('DD-MM-YYYY'))
                            .attr("data-content", item.announcement_view_description)
                            .attr("readStatus", item.announcement_is_read)
                            .attr("data-announcement-id", item.announcement_id)
                            .attr("data-desc", item.announcement_description);

                        var timelinePointWrapper = $("<span>")
                            .addClass("timeline-point-wrapper");

                        var timelinePoint = $("<span>")
                            .addClass("timeline-point timeline-point-warning");

                        var timelineEvent = $("<div>").addClass("timeline-event");

                        var timelineHeader = $("<div>")
                            .addClass("timeline-header border-bottom mb-3");

                        var h6 = $("<h6>")
                            .addClass("mb-0")
                            .html(item.announcement_title + " <i style='font-size:10px'>("+moment(item.announcement_start_date).format('DD-MM-YYYY')+")</i>");

                        var textMuted = $("<span>")
                            .addClass("text-muted")
                            .text(item.announcement_view_description);

                        timelineHeader.append(h6, textMuted);
                        timelineEvent.append(timelineHeader);
                        timelinePointWrapper.append(timelinePoint);
                        timelineItem.append(timelinePointWrapper, timelineEvent);
                        timeline.append(timelineItem);

                        if (item.announcement_is_read === true && totalUnread !== 0) {
                            console.log(totalUnread, 'unread')
                            timelineItem.css("display", "none");
                        } else {
                            console.log(item.announcement_title, 'title')
                            timelineItem.css("display", "block");
                        }
                    });

                    const toogle = $("<button>")
                        .attr("id", "toggleTimeline")
                        .addClass("accordion-button")
                        .attr("data-bs-toggle", "collapse")
                        .text("Show More")
                        .css({
                            display: "block"
                        });

                    $(".body-pengumuman").append(timeline);
                    $(".body-pengumuman").append(toogle);

                    const timelineItems = document.querySelectorAll('.timeline-item');
                    const toggleButton = document.getElementById('toggleTimeline');

                    $(".body-pengumuman .accordion-button").css("text-align", "center");

                    // Show/hide additional timeline items when the button is clicked
                    toggleButton.addEventListener('click', () => {
                        

                        if(toggleButton.textContent === 'Show More') {
                            for (let i = 0; i < timelineItems.length; i++) {
                                timelineItems[i].style.display = 'block';
                            }

                            toggleButton.textContent = 'Show Less';
                        } else {
                            for (let i = 0; i < timelineItems.length; i++) {
                                const readStatus = timelineItems[i].getAttribute("readStatus");
                                if (readStatus == 'true') {
                                    timelineItems[i].style.display = "none";
                                }
                            }

                            toggleButton.textContent = 'Show More';
                        }
                    });

                    // Show the toggle button if there are more items than just the unread ones
                    if (data.data.length > data.unread && data.unread !== 0) {
                        toggleButton.style.display = 'block';
                    } else {
                        toggleButton.style.display = 'none'; // Hide the button if no more items to show
                    }

                    $(".custom-tutup").css("display", "block");
                    $(".spinner-box").fadeOut();
                } else {
                    // If length is 0, create the "Tidak ada pengumuman" message
                    $(".custom-tutup").css("display", "none");

                    $(".body-pengumuman").append(
                        '<div class="d-flex justify-content-center mt-0"><p class="mt-0"><i>Tidak ada pengumuman</i></p></div>'
                    );
                    $(".spinner-box").fadeOut();
                }
                },
                error: function() {
                    // Handle errors if the request fails
                    $(".body-pengumuman p").remove(); // Remove any existing message
                    $(".body-pengumuman").append(
                        '<div class="d-flex justify-content-center mt-0"><p class="mt-0"><i>Terjadi kesalahan pada pengumuman.</i></p></div>'
                    );
                }
            });
        }

        loadTimelineData();

        let selectedId
        let selectedRead = false;

        $(".body-pengumuman").on("click", ".timeline-item", function() {
            var announcementId = $(this).data("announcement-id");
            var announcementTitle = $(this).attr("data-title");
            var announcementDescription = $(this).attr("data-desc");
            var announcementStDate = $(this).attr("data-stdate");

            // Set the announcement-id attribute for the delete button

            selectedId = announcementId;

            console.log(selectedId);

            // Populate the modal with the content
            $("#timelineModalLabel").html(announcementTitle + " <i style='font-size:10px'>("+announcementStDate+")</i>");
            $("#modalContent").text(announcementDescription);

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            if($(this).attr("readStatus") == "false") {
                selectedRead = false;
                $.ajax({
                    url: "/karyawan/pengumuman/update-pengumuman",
                    method: "POST", // Change to the appropriate HTTP method if needed
                    headers: {
                        "X-CSRF-TOKEN": csrfToken // Include the CSRF token in the request headers
                    },
                    data: {
                        announcement_id: announcementId,
                        tipe: 'READ'
                    },
                    success: function(response) {
                        unread = unread - 1;
                    },
                    error: function(error) {
                        toastr.error('Terjadi masalah saat membuka detail pengumuman');
                        console.error("Error updating announcement: " + error);
                    }
                });
            } else {
                selectedRead = true;
            }

            // Show the modal
            $('#timelineModal').modal('show');
        });

        $("#timelineModal").on("click", "#btn-delete", function(e) {
            e.preventDefault();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const jsonData = {}; // To store the JSON data

            var announcementId = selectedId;

            Swal.fire({
                html: 'Apakah anda ingin menghapus pengumuman ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
                    preConfirm: async () => {
                    Swal.showLoading();
                    const currentDate = new Date();
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    const requestData = {
                        _token: csrfToken,
                        announcement_id : announcementId,
                        tipe: "CLEAR"
                    };

                    try {
                        const response = await fetch("{{ route('karyawan.pengumuman.update-pengumuman') }}", {
                        method: 'POST',
                        body: JSON.stringify(requestData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                        });

                        console.log(response, 'response')

                        if (!response.ok) {
                            throw new Error(await response.text());
                        }

                        Swal.close(); // Close the modal
                        return response.json();
                    } catch (error) {
                        Swal.showValidationMessage(`Terjadi masalah saat menghapus pengumuman`);
                        // toastr.error('Terjadi masalah saat menghapus pengumuman');
                        throw error;
                    }
                    },
                    allowOutsideClick: () => false
            }).then((result) => {
                if (!result.value) {
                return;
                }

                if (!result.value.success) {
                Swal.fire({
                    title: result.value.message,
                    confirmButtonText: "Ok",
                    type: 'error'
                });
                return;
                }

                $(`.timeline-item[data-announcement-id="${announcementId}"]`).remove();

                console.log('length', $(".timeline").children().length);
                console.log('unread', unread);

                // Check if the timeline is empty
                if ($(".timeline").children().length === 0) {
                    // Hide the "Show More" / "Show Less" toggle button
                    $("#toggleTimeline").hide();

                    // Hide the "Delete All" button
                    $("#deleteAllAnnounce").hide();

                    // Add the "Tidak ada pengumuman" message
                    $(".body-pengumuman").append(
                        '<div class="d-flex justify-content-center mt-0"><p class="mt-0"><i>Tidak ada pengumuman</i></p></div>'
                    );
                } else if($(".timeline").children().length <= unread) {
                    console.log("testing")
                    $("#toggleTimeline").hide();
                }

                toastr.success(result.value.message);
                $('#timelineModal').modal('hide');
            });
        });

        $(".body-pengumuman").on("click", "#deleteAllAnnounce", function(e) {
            e.preventDefault();

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                html: 'Apakah anda ingin menghapus semua pengumuman?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
                    preConfirm: async () => {
                    Swal.showLoading();
                    const currentDate = new Date();
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    
                    const requestData = {
                        _token: csrfToken,
                        tipe: "CLEARALL"
                    };

                    try {
                        const response = await fetch("{{ route('karyawan.pengumuman.update-pengumuman') }}", {
                        method: 'POST',
                        body: JSON.stringify(requestData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                        });

                        console.log(response, 'response')

                        if (!response.ok) {
                            throw new Error(await response.text());
                        }

                        Swal.close(); // Close the modal
                        return response.json();
                    } catch (error) {
                        Swal.showValidationMessage(`Terjadi masalah saat menghapus pengumuman`);
                        // toastr.error('Terjadi masalah saat menghapus pengumuman');
                        throw error;
                    }
                    },
                    allowOutsideClick: () => false
            }).then((result) => {
                if (!result.value) {
                return;
                }

                if (!result.value.success) {
                Swal.fire({
                    title: result.value.message,
                    confirmButtonText: "Ok",
                    type: 'error'
                });
                return;
                }

                $(".timeline-item").remove();

                // Hide the "Show More" / "Show Less" toggle button
                $("#toggleTimeline").hide();

                // Hide the "Delete All" button
                $("#deleteAllAnnounce").hide();

                // Add the "Tidak ada pengumuman" message
                $(".body-pengumuman").append(
                    '<div class="d-flex justify-content-center mt-0"><p class="mt-0"><i>Tidak ada pengumuman</i></p></div>'
                );

                toastr.success(result.value.message);
            });
        });
    })
</script>