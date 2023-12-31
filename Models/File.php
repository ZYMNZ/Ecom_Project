<?php
    class File {

        private static function canImageBeOpened(string $imagePath) : array
        {
            /*
             * Check if a certain file can be opened
             */
            $imageCanBeOpened = false;

            $openedImage = fopen($imagePath, "rb");

            if($openedImage)
            {
                $imageCanBeOpened = true;
            }


            return [
                "imageCanBeOpened" => $imageCanBeOpened,
                "fileResource" => $openedImage
            ];
        }

        private static function checkFileContentsAreSame($clientImageResource, $serverImageResource) : array
        {
            /*
             * Check if a file when read are the same to check if the file contents are the same
            */
            define("READ_SIZE", 4096);
            $fileContentsAreSame = true;
            while(!feof($clientImageResource) && !feof($serverImageResource))
            {
                if(fread($clientImageResource, READ_SIZE) !== fread($serverImageResource, READ_SIZE))
                {
                    $fileContentsAreSame = false;
                }
            }

            return [
                "fileContentsAreSame" => $fileContentsAreSame,
                "fileResourceClient" => $clientImageResource,
                "fileResourceServer" => $serverImageResource
            ];
        }

        private static function checkFilePointerLocationsAreSame($clientImageResource, $serverImageResource) : array
        {
            /*
             * Check if a file's contents has been read before the other file's contents
             */
            $filePointerSameLocation = true;
            if(feof($clientImageResource) !== feof($serverImageResource))
            {
                $filePointerSameLocation = false;
            }
            return [
                "filePointerSameLocation" => $filePointerSameLocation,
                "fileResourceClient" => $clientImageResource,
                "fileResourceServer" => $serverImageResource
            ];
        }

        public static function getProductImageName(string $pFileDirectoryToUploadTo, string $fileExtension, int $productId)
        {
            $fileDirectoryToUploadTo = $pFileDirectoryToUploadTo . $_SESSION["user_id"] . "_" . $productId . "." . $fileExtension;
            return $fileDirectoryToUploadTo;
        }

        public static function uploadProductImage(string $targetDirectory, string $imageName, string $temporaryServerImageDirectory) : array
        {
            define("IMAGE_TYPES_ALLOWED", [
                "jpg",
                "jpeg",
                "png"
            ]);

            define("MAX_IMAGE_SIZE", 30_000_000);
            $shouldUploadImage = true;
            $uploadDirectory = $targetDirectory . basename($imageName);



            $fileByteSizeIsValid = self::fileIsUnderSize($_FILES["productImage"]["size"], MAX_IMAGE_SIZE);



            $fileIsAValidImage = self::fileHasExtension($uploadDirectory, IMAGE_TYPES_ALLOWED);
            if(!$fileIsAValidImage)
            {
                $shouldUploadImage = false;

                return [
                    "shouldUploadImage" => $shouldUploadImage,
                    "imageIsSame" => false
                ];
            }

            if(!$fileByteSizeIsValid)
            {
                $shouldUploadImage = false;
                return [
                    "shouldUploadImage" => $shouldUploadImage,
                    "imageIsSame" => false
                ];
            }


            $fileUploadedIsSame = self::checkFileIsSame($temporaryServerImageDirectory, $uploadDirectory);

            if($fileUploadedIsSame)
            {
                $shouldUploadImage = false;
                return [
                    "shouldUploadImage" => $shouldUploadImage,
                    "imageIsSame" => true
                ];
            }

            if($shouldUploadImage) {
                if($_GET["id"])
                {
                    self::deleteAssocProductImage();
                }
                if(move_uploaded_file($temporaryServerImageDirectory, $uploadDirectory)) {
                    // If there was a success when uploading the file, say that the image has been uploaded successfully through this boolean
                    $shouldUploadImage = true;
                }
                else {
                    $shouldUploadImage = false;

                }
            }
            else {
                $shouldUploadImage = false;
            }

            return [
                "shouldUploadImage" => $shouldUploadImage,
                "imageIsSame" => false
            ];
        }

        private static function fileHasExtension(string $pPathToFile, array $listOfFileTypesAllowed) : bool
        {
            $fileType = strtolower(pathinfo($pPathToFile, PATHINFO_EXTENSION));

            /*
             * Check if the file in a certain directory has extensions through the
             * listOfFileTypes array
             */
            $fileIsOfCorrectExtension = false;
            foreach($listOfFileTypesAllowed as $stringFileType) {

                if($fileType === $stringFileType)
                {
                    $fileIsOfCorrectExtension = true;
                    break;
                }
            }
            return $fileIsOfCorrectExtension;
        }

        private static function fileIsUnderSize(int $fileSize, int $sizeInBytes) : bool
        {
            /*
             * Check if the file is under of a certain size in bytes
             */
            $fileIsUnderSizeInBytes = false;
            if($fileSize < $sizeInBytes)
            {
                $fileIsUnderSizeInBytes = true;
            }


            return $fileIsUnderSizeInBytes;
        }

        private static function checkFileIsSame(string $clientImagePath, string $uploadFilePath) : bool
        {
            $fileIsSame = true;
            /*
             * If file does not exist, upload is ok because we do not need to check
             */

            if(!file_exists($uploadFilePath)) {

                $fileIsSame = false;
                return $fileIsSame;
            }

            /*
             * Check if the file types between the two files are the same
             * because it will tell us if the files are the same or not
            */
            if(filetype($clientImagePath) !== filetype($clientImagePath))
            {
                $fileIsSame = false;
                return $fileIsSame;
            }

            /*
             * Check if the file size between the two files are the same
             * because it will tell us if the files are the same or not
             * */
            if(filesize($clientImagePath) !== filesize($uploadFilePath))
            {
                $fileIsSame = false;
                return $fileIsSame;
            }

            /*
             * Check if the client file (uploaded file) and the server file can be opened
             * because it will help us tell whether the two files are the same or not
             * */
            $clientFileCanBeOpened = self::canImageBeOpened($clientImagePath);
            if(!$clientFileCanBeOpened["imageCanBeOpened"])
            {
                $clientFileCanBeOpened["fileResource"]->close();
                $fileIsSame = false;
                return $fileIsSame;
            }


            $serverFileCanBeOpened = self::canImageBeOpened($uploadFilePath);
            if(!$serverFileCanBeOpened["imageCanBeOpened"])
            {
                $serverFileCanBeOpened["fileResource"]->close();
                $fileIsSame = false;
                return $fileIsSame;
            }

            /*
             * Check if the contents of both files are the same
             * because it will help us tell whether two files are the same or not
             * */
            $fileContentsAreSame = self::checkFileContentsAreSame(
                $clientFileCanBeOpened["fileResource"],
                $serverFileCanBeOpened["fileResource"]
            );
            if(!$fileContentsAreSame["fileContentsAreSame"])
            {
                $fileContentsAreSame["fileResourceClient"]->close();
                $fileContentsAreSame["fileResourceServer"]->close();
                $fileIsSame = false;
                return $fileIsSame;
            }

            /*
             * Check whether the file pointer stopped at different locations
             * because it will help us tell whether two files are the same or not
             * */
            $filePointerLocationsAreSame = self::checkFilePointerLocationsAreSame(
                $fileContentsAreSame["fileResourceClient"],
                $fileContentsAreSame["fileResourceServer"]
            );
            if(!$filePointerLocationsAreSame["filePointerSameLocation"])
            {
                $filePointerLocationsAreSame["fileResourceClient"]->close();
                $filePointerLocationsAreSame["fileResourceServer"]->close();
                $fileIsSame = false;
                return $fileIsSame;
            }

            return $fileIsSame;
        }

        private static function deleteAssocProductImage()
        {
            /*
             * Check if there is another associated product image with the current product
             * and delete it because we do not need it
             */

            /*
             * The file extension here does not matter, we just want the file name without the extension because we need it
             * when checking for files with the same name
             */
            $fileName = pathinfo(self::getProductImageName(
                Product::PRODUCT_IMAGE_UPLOAD_PATH,
                ".jpg",
                $_GET["id"]
            ));
            /*
             * Search through the directory of product images to delete files with the same file name
             * because we want to delete those before uploading the new image
             */

            $imageAssocProduct = glob(Product::PRODUCT_IMAGE_UPLOAD_PATH . $fileName["filename"] . "*");
            /*
             * Delete the image found for the same product because we do not need it anymore
             */
            if($imageAssocProduct) {
                unlink($imageAssocProduct[0]);
            }


        }
    }


?>