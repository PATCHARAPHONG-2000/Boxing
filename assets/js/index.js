let matchCounter = 1; // ตัวนับลำดับที่ของแมตช์
let isMatchNameSaved = false; // ตัวแปรสถานะ ตรวจสอบว่ากดบันทึกชื่อแมตช์แล้วหรือยัง

// ฟังก์ชันสำหรับบันทึกข้อมูลและแสดงในตาราง
document
    .getElementById("matchForm")
    .addEventListener("submit", function (event) {
        event.preventDefault(); // ป้องกันการรีเฟรชหน้าเว็บ

        // ดึงข้อมูลจากฟอร์ม
        const matchName = document.getElementById("Match_Name").value;
        const redCorner = document.getElementById("Person_Red").value;
        const blueCorner = document.getElementById("Person_Blue").value;
        const redImage = document.getElementById("Image_Red").files[0];
        const blueImage = document.getElementById("Image_Blue").files[0];

        // ตรวจสอบข้อมูลว่าครบถ้วนหรือไม่
        if (!matchName || !redCorner || !blueCorner) {
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
            return;
        }

        // สร้างตัวอย่างรูปภาพ (ถ้ามีการอัปโหลด)
        const redImageURL = redImage ? URL.createObjectURL(redImage) : "";
        const blueImageURL = blueImage ? URL.createObjectURL(blueImage) : "";

        // เพิ่มข้อมูลใน DataTable
        const table = $("#matchDataTable").DataTable();
        table.row
            .add([
                matchCounter,
                redCorner,
                blueCorner,
                `<img src="${redImageURL}" alt="Red Corner Image" style="width: 50px; height: 50px; object-fit: cover;">`,
                `<img src="${blueImageURL}" alt="Blue Corner Image" style="width: 50px; height: 50px; object-fit: cover;">`,
                `
            <button type="button" class="btn btn-info btn-sm me-2" onclick="editMatch(${matchCounter})">
                <i class="fas fa-edit"></i> แก้ไข
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteMatch(${matchCounter})">
                <i class="fas fa-trash"></i> ลบ
            </button>
        `,
            ])
            .draw();

        // เพิ่มตัวนับลำดับที่
        matchCounter++;

        // เคลียร์ฟอร์มหลังจากเพิ่มข้อมูล
        document.getElementById("Person_Red").value = "";
        document.getElementById("Person_Blue").value = "";
        document.getElementById("Image_Red").value = "";
        document.getElementById("Image_Blue").value = "";

        // แจ้งเตือนเมื่อบันทึกสำเร็จ
        Swal.fire({
            title: "สำเร็จ!",
            text: "ข้อมูลของคุณได้ถูกบันทึกแล้ว.",
            icon: "success",
            confirmButtonText: "ตกลง",
        });
    });

function editMatch(counter) {
    // ใช้ DataTable เพื่อเลือกแถวที่ใช้งานอยู่ในปัจจุบัน
    const table = $("#matchDataTable").DataTable();
    const row = table.row(counter - 1).node(); // ดึงแถวตามลำดับที่ปรากฏใน DataTable
    if (!row) return; // ถ้าแถวไม่พบก็หยุดการทำงาน

    const redName = row.cells[1].innerText;
    const blueName = row.cells[2].innerText;
    const redImageSrc = row.cells[3].querySelector("img").src;
    const blueImageSrc = row.cells[4].querySelector("img").src;

    Swal.fire({
        title: "แก้ไขข้อมูลแมตช์",
        html: `
            <form id="editForm" style="display: flex; gap: 40px; padding: 30px;">
                <!-- คอลัมน์ฝ่ายสีแดง -->
                <div style="flex: 1;">
                    <label for="editRedName">ชื่อฝ่ายสีแดง:</label>
                    <input type="text" id="editRedName" value="${redName}" class="swal2-input">
                    
                    <div id="redDropZone" 
                        style="border: 2px dashed #ccc; padding: 20px; text-align: center; margin-top: 10px;"
                        ondrop="handleDrop(event, 'redDropZone', 'editRedImagePreview')" 
                        ondragover="handleDragOver(event)">
                        ลากและวางรูปภาพที่นี่
                        <div id="editRedImagePreview" style="margin-top: 10px;">
                            ${redImageSrc
                        ? `<img src="${redImageSrc}" style="width: 100px; height: 100px; object-fit: cover;">`
                        : ""
                    }
                        </div>
                    </div>
                    <input type="file" id="editRedImage" accept="image/*" style="display: none;">
                </div>

                <!-- คอลัมน์ฝ่ายสีน้ำเงิน -->
                <div style="flex: 1;">
                    <label for="editBlueName">ชื่อฝ่ายสีน้ำเงิน:</label>
                    <input type="text" id="editBlueName" value="${blueName}" class="swal2-input">
                    
                    <div id="blueDropZone" 
                        style="border: 2px dashed #ccc; padding: 20px; text-align: center; margin-top: 10px;"
                        ondrop="handleDrop(event, 'blueDropZone', 'editBlueImagePreview')" 
                        ondragover="handleDragOver(event)">
                        ลากและวางรูปภาพที่นี่
                        <div id="editBlueImagePreview" style="margin-top: 10px;">
                            ${blueImageSrc
                        ? `<img src="${blueImageSrc}" style="width: 100px; height: 100px; object-fit: cover;">`
                        : ""
                    }
                        </div>
                    </div>
                    <input type="file" id="editBlueImage" accept="image/*" style="display: none;">
                </div>
            </form>`,
        showCancelButton: true,
        confirmButtonText: "บันทึกการเปลี่ยนแปลง",
        cancelButtonText: "ยกเลิก",
        width: "800px",
        preConfirm: () => {
            const editedRedName = document.getElementById("editRedName").value;
            const editedBlueName =
                document.getElementById("editBlueName").value;

            if (!editedRedName || !editedBlueName) {
                Swal.showValidationMessage("กรุณากรอกชื่อทั้งสองฝ่าย");
                return false;
            }

            return {
                editedRedName,
                editedBlueName,
                editedRedImage: document.getElementById("editRedImage").files[0],
                editedBlueImage:
                    document.getElementById("editBlueImage").files[0],
            };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // ใช้ DataTables API เพื่ออัปเดตข้อมูลในแถวที่เลือก
            const row = table.row(counter - 1).node(); // ดึงแถวตามลำดับที่ปรากฏใน DataTable
            row.cells[1].innerText = result.value.editedRedName;
            row.cells[2].innerText = result.value.editedBlueName;

            if (result.value.editedRedImage) {
                const redImageURL = URL.createObjectURL(
                    result.value.editedRedImage
                );
                row.cells[3].innerHTML = `<img src="${redImageURL}" style="width: 50px; height: 50px; object-fit: cover;">`;
            }
            if (result.value.editedBlueImage) {
                const blueImageURL = URL.createObjectURL(
                    result.value.editedBlueImage
                );
                row.cells[4].innerHTML = `<img src="${blueImageURL}" style="width: 50px; height: 50px; object-fit: cover;">`;
            }

            Swal.fire("สำเร็จ!", "ข้อมูลได้รับการแก้ไขแล้ว!", "success");
        }
    });
}

function selectMatchType(type) {
    document.getElementById("Match_Type").value = type; // อัปเดตค่าที่เลือกในฟอร์ม
    document.getElementById("dropdownMatchType").textContent = type; // เปลี่ยนข้อความในปุ่ม
}

function clearAllData() {
    // ล้างค่าข้อมูลทั้งหมดในฟอร์ม
    document.getElementById("matchForm").reset();
    document.getElementById("dropdownMatchType").textContent =
        "เลือกประเภทมวย"; // รีเซ็ตข้อความปุ่ม
    document.getElementById("Match_Type").value = ""; // รีเซ็ตค่าประเภทมวย

    // ล้างข้อมูลในตาราง
    const tableBody = document.getElementById("matchTableBody");
    tableBody.innerHTML = ""; // ลบแถวทั้งหมดในตาราง

    alert("ลบข้อมูลทั้งหมดสำเร็จ");
}

function toggleEdit() {
    const matchNameInput = document.getElementById("Match_Name");
    const saveButton = document.getElementById("lockMatchNameButton");
    const editButton = document.getElementById("editMatchNameButton");

    // เปิดให้แก้ไขช่องป้อนข้อความ
    matchNameInput.disabled = false;

    // ปรับสถานะปุ่ม
    saveButton.disabled = false;
    editButton.disabled = true; // ปิดปุ่ม "แก้ไข"

    // รีเซ็ตสถานะการบันทึก
    isMatchNameSaved = false;
}

function saveMatchName() {
    const matchNameInput = document.getElementById("Match_Name");
    const saveButton = document.getElementById("lockMatchNameButton");
    const editButton = document.getElementById("editMatchNameButton");

    // ล็อคช่องป้อนข้อความไม่ให้แก้ไข
    matchNameInput.disabled = true;

    // เปลี่ยนสถานะของปุ่ม
    saveButton.disabled = true;
    editButton.disabled = false;

    // ตั้งสถานะว่าได้บันทึกชื่อแมตช์แล้ว
    isMatchNameSaved = true;

    // แจ้งเตือนผู้ใช้
    Swal.fire({
        title: "ล็อคสำเร็จ!",
        text: "ชื่อแมตช์ของคุณถูกล็อคเรียบร้อยแล้ว",
        icon: "success",
        confirmButtonText: "ตกลง",
    });
}

function deleteMatch(counter) {
    // ใช้ DataTable เพื่อเลือกแถวที่ใช้งานอยู่ในปัจจุบัน
    const table = $("#matchDataTable").DataTable();
    const row = table.row(counter - 1).node(); // ดึงแถวตามลำดับที่ปรากฏใน DataTable
    if (!row) return; // ถ้าแถวไม่พบก็หยุดการทำงาน

    Swal.fire({
        title: "คุณแน่ใจหรือไม่?",
        text: "การลบข้อมูลนี้จะไม่สามารถกู้คืนได้!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "ลบข้อมูล",
        cancelButtonText: "ยกเลิก",
    }).then((result) => {
        if (result.isConfirmed) {
            // ลบแถวโดยใช้ DataTables
            table
                .row(counter - 1)
                .remove()
                .draw(); // ลบแถวจาก DataTable

            // แจ้งเตือนว่าลบสำเร็จ
            Swal.fire({
                title: "ลบสำเร็จ!",
                text: "ข้อมูลของคุณถูกลบแล้ว.",
                icon: "success",
                confirmButtonText: "ตกลง",
            });
        }
    });
}

function handleDrop(event, dropZoneId, previewId) {
    event.preventDefault();

    // รับไฟล์จากเหตุการณ์การลากและวาง
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];

        // ตรวจสอบว่าเป็นไฟล์รูปภาพ
        if (file.type.startsWith("image/")) {
            const preview = document.getElementById(previewId);
            const dropZone = document.getElementById(dropZoneId);

            // แสดงภาพตัวอย่าง
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = `<img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover;">`;
            };
            reader.readAsDataURL(file);

            // อัปเดต input file ที่ซ่อนอยู่
            if (dropZoneId === "redDropZone") {
                document.getElementById("editRedImage").files = files;
            } else if (dropZoneId === "blueDropZone") {
                document.getElementById("editBlueImage").files = files;
            }
        } else {
            Swal.fire("ข้อผิดพลาด!", "กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น", "error");
        }
    }
}
$(document).ready(function () {
    // เริ่มต้น DataTable
    $("#matchDataTable").DataTable({
        paging: true, // เปิดใช้งานการแบ่งหน้า
        searching: true, // เปิดใช้งานการค้นหา
        ordering: true, // เปิดใช้งานการจัดเรียง
        info: true, // แสดงข้อมูลสรุป
        language: {
            search: "ค้นหา:", // เปลี่ยนข้อความการค้นหา
            lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            paginate: {
                next: "ถัดไป",
                previous: "ก่อนหน้า",
            },
        },
    });
});