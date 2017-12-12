package com.gionee.gioneeabc.bean;

import java.io.Serializable;

/**
 * Created by Linchpin25 on 3/2/2016.
 */
public class UserBean implements Serializable{
    private int userId;
    private String userName;
    private String userEmail;
    private String userImage;
    private String userImageServerUrl;
    private String userImageLocalUrl;
    private String status;

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getUserImageServerUrl() {
        return userImageServerUrl;
    }

    public void setUserImageServerUrl(String userImageServerUrl) {
        this.userImageServerUrl = userImageServerUrl;
    }

    public String getUserImageLocalUrl() {
        return userImageLocalUrl;
    }

    public void setUserImageLocalUrl(String userImageLocalUrl) {
        this.userImageLocalUrl = userImageLocalUrl;
    }

    public String getUserImage() {
        return userImage;
    }

    public void setUserImage(String userImage) {
        this.userImage = userImage;
    }

    public int getUserId() {
        return userId;
    }

    public void setUserId(int userId) {
        this.userId = userId;
    }

    public String getUserName() {
        return userName;
    }

    public void setUserName(String userName) {
        this.userName = userName;
    }

    public String getUserEmail() {
        return userEmail;
    }

    public void setUserEmail(String userEmail) {
        this.userEmail = userEmail;
    }


}
