# user-rad
radius admin for vocational education commision
- นำเข้าข้อมูลจากแฟ้มข้อมูล std  (.csv, comma)
- นำเข้าข้อมูลจากแฟ้มข้อมูล (.csv, comma)  ที่มีหัว column ดังนี้
-- "username","password","fname","lname","groupname"
- การติดตั้งเพื่อใช้งานเบื้องต้น
-- กำหนดค่าคอนฟิกใน include/config.php
-- ติดตั้ง table เพิ่มเติมจาก schema/user-rad.sql
-- เพิ่ม user admin ลงในตาราง users
-- Login เข้าระบบโดยใช้ user admin
-- การโอนข้อมูลเข้าระบบ
- เมนู จัดการระบบ
-- เมนู จัดการไฟล์
--- อัพโหลดไฟล์
--- ทำการตรวจสอบไฟล์
--- โอนข้อมูลเข้าระบบ
--- ให้กำหนดกลุ่มผู้ใช้และตั้งค่าการดาวน์โหลด/อัพโหลด
-- เมนู แก้ไขตั้งค่ากลุ่มผู้ใช้
--- จัดการ ลบ แก้ไข หรือเพิ่มข้อมูล กลุ่มผู้ใช้
--- หลังจากนั้นทำการโอนข้อมูลเข้าระบบ
-- เมนู โอน/ลบข้อมูล           
