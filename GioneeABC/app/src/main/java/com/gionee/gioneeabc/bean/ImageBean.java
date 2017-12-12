package com.gionee.gioneeabc.bean;

/**
 * Created by Linchpin25 on 3/2/2016.
 */
public class ImageBean {
    private int imageId;
    private String imageName;
    private String imageType;
    private String imageLocalPath;
    private String imageServerPath;
    private int modelId;
    private String imageTitle;
    private byte[] imageByte;

    public ImageBean() {
    }
    public ImageBean(int image_Id, String name,  String title,String path) {
        this.imageId = image_Id;
        this.imageName = name;
        this.imageTitle=title;
        this.imageLocalPath = path;

    }
    public ImageBean(int imageId, String imageName, String imageType, String imageLocalPath, String imageServerPath, int modelId) {
        this.imageId = imageId;
        this.imageName = imageName;
        this.imageType = imageType;
        this.imageLocalPath = imageLocalPath;
        this.imageServerPath = imageServerPath;
        this.modelId = modelId;
    }
    public ImageBean(int imageId, String imageName, String imageType, String imageLocalPath, int modelId) {
        this.imageId = imageId;
        this.imageName = imageName;
        this.imageType = imageType;
        this.imageLocalPath = imageLocalPath;
        this.modelId = modelId;
    }
    public ImageBean(int imageId, String imageName, byte[] imageByte) {
        this.imageId = imageId;
        this.imageName = imageName;
        this.imageByte = imageByte;
    }

    public byte[] getImageByte() {
        return imageByte;
    }

    public void setImageByte(byte[] imageByte) {
        this.imageByte = imageByte;
    }

    public String getImageTitle() {
        return imageTitle;
    }

    public void setImageTitle(String imageTitle) {
        this.imageTitle = imageTitle;
    }

    public int getImageId() {
        return imageId;
    }

    public void setImageId(int imageId) {
        this.imageId = imageId;
    }

    public String getImageName() {
        return imageName;
    }

    public void setImageName(String imageName) {
        this.imageName = imageName;
    }

    public String getImageType() {
        return imageType;
    }

    public void setImageType(String imageType) {
        this.imageType = imageType;
    }

    public String getImageLocalPath() {
        return imageLocalPath;
    }

    public void setImageLocalPath(String imageLocalPath) {
        this.imageLocalPath = imageLocalPath;
    }

    public String getImageServerPath() {
        return imageServerPath;
    }

    public void setImageServerPath(String imageServerPath) {
        this.imageServerPath = imageServerPath;
    }

    public int getModelId() {
        return modelId;
    }

    public void setModelId(int modelId) {
        this.modelId = modelId;
    }
}
