<?php
    
class ImageUpload
{
    public static function uploadProfile(array $file, string $currentPhoto): string
    {
        $uploadDir = "../uploads/profile/";

        // No new image uploaded
        if ($file["error"] === UPLOAD_ERR_NO_FILE) {
            return $currentPhoto;
        }

        // Upload error
        if ($file["error"] !== UPLOAD_ERR_OK) {
            die("Image upload failed.");
        }

        $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

        $extension = strtolower(
            pathinfo($file["name"], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed)) {
            die("Invalid image type.");
        }

        if ($file["size"] > 5 * 1024 * 1024) {
            die("Image too large.");
        }

        $photoName = uniqid("profile_", true) . "." . $extension;

        if (!move_uploaded_file(
            $file["tmp_name"],
            $uploadDir . $photoName
        )) {
            die("Unable to save uploaded image.");
        }

        // Delete old image
        if (
            $currentPhoto != "default.png" &&
            file_exists($uploadDir . $currentPhoto)
        ) {
            unlink($uploadDir . $currentPhoto);
        }

        return $photoName;
    }
}