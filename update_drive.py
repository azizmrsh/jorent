import os
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload

CLIENT_ID = os.getenv("CLIENT_ID")
CLIENT_SECRET = os.getenv("CLIENT_SECRET")
REFRESH_TOKEN = os.getenv("REFRESH_TOKEN")

# معرف المجلد في Google Drive
FOLDER_ID = "1oIHaByqEWaRjT6yxQaoVS_3evy1jylQT"

def get_credentials():
    """إنشاء بيانات الاعتماد باستخدام Refresh Token"""
    creds_data = {
        "token": "",
        "refresh_token": REFRESH_TOKEN,
        "token_uri": "https://oauth2.googleapis.com/token",
        "client_id": CLIENT_ID,
        "client_secret": CLIENT_SECRET
    }
    creds = Credentials.from_authorized_user_info(creds_data)
    return creds

def upload_file_to_drive(filename, mime_type, folder_id):
    """رفع ملف إلى Google Drive"""
    creds = get_credentials()
    service = build('drive', 'v3', credentials=creds)

    file_metadata = {
        'name': os.path.basename(filename),
        'parents': [folder_id]
    }

    media = MediaFileUpload(filename, mimetype=mime_type)
    
    try:
        print(f"Attempting to upload: {filename}")
        file = service.files().create(
            body=file_metadata,
            media_body=media,
            fields='id'
        ).execute()

        file_id = file.get('id')
        print(f"File uploaded successfully: {filename} - View it here: https://drive.google.com/file/d/{file_id}/view")

    except Exception as e:
        print(f"Error uploading {filename}: {e}")

def upload_all_files(folder_id):
    """رفع جميع الملفات من جذر المشروع إلى Google Drive"""
    print("Scanning files for upload...")

    for root, _, files in os.walk("."):
        for file in files:
            filepath = os.path.join(root, file)

            #] عرض الملفات التي تم العثور عليها
            print(f"Found file: {filepath}")

            # تعليق شرط الاستثناء - يمكن تفعيله لاحقاً
            # if file.startswith(".") or file.endswith(".py"):
            #     continue

            mime_type = "application/octet-stream"
            upload_file_to_drive(filepath, mime_type, folder_id)

if __name__ == "__main__":
    upload_all_files(FOLDER_ID)
