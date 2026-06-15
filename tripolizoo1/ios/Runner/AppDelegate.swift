import Flutter
import Photos
import UIKit

@main
@objc class AppDelegate: FlutterAppDelegate, FlutterImplicitEngineDelegate {
  private var ticketImageChannel: FlutterMethodChannel?

  override func application(
    _ application: UIApplication,
    didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
  ) -> Bool {
    return super.application(application, didFinishLaunchingWithOptions: launchOptions)
  }

  func didInitializeImplicitFlutterEngine(_ engineBridge: FlutterImplicitEngineBridge) {
    GeneratedPluginRegistrant.register(with: engineBridge.pluginRegistry)

    let channel = FlutterMethodChannel(
      name: "tripolizoo/ticket_images",
      binaryMessenger: engineBridge.applicationRegistrar.messenger()
    )
    channel.setMethodCallHandler { [weak self] call, result in
      guard call.method == "saveImage" else {
        result(FlutterMethodNotImplemented)
        return
      }
      guard
        let arguments = call.arguments as? [String: Any],
        let typedData = arguments["bytes"] as? FlutterStandardTypedData,
        let image = UIImage(data: typedData.data)
      else {
        result(
          FlutterError(
            code: "invalid_image",
            message: "تعذر قراءة صورة التذكرة",
            details: nil
          )
        )
        return
      }
      self?.saveToPhotos(image: image, result: result)
    }
    ticketImageChannel = channel
  }

  private func saveToPhotos(image: UIImage, result: @escaping FlutterResult) {
    let saveImage = {
      PHPhotoLibrary.shared().performChanges({
        PHAssetChangeRequest.creationRequestForAsset(from: image)
      }) { success, error in
        DispatchQueue.main.async {
          if success {
            result(true)
          } else {
            result(
              FlutterError(
                code: "save_failed",
                message: error?.localizedDescription ?? "تعذر حفظ الصورة",
                details: nil
              )
            )
          }
        }
      }
    }

    if #available(iOS 14, *) {
      PHPhotoLibrary.requestAuthorization(for: .addOnly) { status in
        if status == .authorized || status == .limited {
          saveImage()
        } else {
          DispatchQueue.main.async {
            result(
              FlutterError(
                code: "permission_denied",
                message: "لم يتم السماح بحفظ الصور",
                details: nil
              )
            )
          }
        }
      }
    } else {
      PHPhotoLibrary.requestAuthorization { status in
        if status == .authorized {
          saveImage()
        } else {
          DispatchQueue.main.async {
            result(
              FlutterError(
                code: "permission_denied",
                message: "لم يتم السماح بحفظ الصور",
                details: nil
              )
            )
          }
        }
      }
    }
  }
}
