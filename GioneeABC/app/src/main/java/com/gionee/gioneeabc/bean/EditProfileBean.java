package com.gionee.gioneeabc.bean;

import java.util.List;

/**
 * Created by root on 24/10/16.
 */
public class EditProfileBean {

        private int count;
    private String status;
    private List<EditProfileData> data;

    public int getCount() {
        return count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public List<EditProfileData> getData() {
        return data;
    }

    public void setData(List<EditProfileData> data) {
        this.data = data;
    }

    private class EditProfileData {
        private int id;
        private String first_name;
        private String last_name;
        private String email;
        private String contact;
        private String status;
        private String gender;
        private String role;
        private String dob;
        private String profile_picture;
        private String city;
        private String state;
        private String zone;
        private String beat_route_id;
        private String rt_code;
        private String nd_name;
        private String rd_name;
        private String sp_name;
        private String sp_id;

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }

        public String getFirst_name() {
            return first_name;
        }

        public void setFirst_name(String first_name) {
            this.first_name = first_name;
        }

        public String getLast_name() {
            return last_name;
        }

        public void setLast_name(String last_name) {
            this.last_name = last_name;
        }

        public String getEmail() {
            return email;
        }

        public void setEmail(String email) {
            this.email = email;
        }

        public String getContact() {
            return contact;
        }

        public void setContact(String contact) {
            this.contact = contact;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getGender() {
            return gender;
        }

        public void setGender(String gender) {
            this.gender = gender;
        }

        public String getRole() {
            return role;
        }

        public void setRole(String role) {
            this.role = role;
        }

        public String getDob() {
            return dob;
        }

        public void setDob(String dob) {
            this.dob = dob;
        }

        public String getProfile_picture() {
            return profile_picture;
        }

        public void setProfile_picture(String profile_picture) {
            this.profile_picture = profile_picture;
        }

        public String getCity() {
            return city;
        }

        public void setCity(String city) {
            this.city = city;
        }

        public String getState() {
            return state;
        }

        public void setState(String state) {
            this.state = state;
        }

        public String getZone() {
            return zone;
        }

        public void setZone(String zone) {
            this.zone = zone;
        }

        public String getBeat_route_id() {
            return beat_route_id;
        }

        public void setBeat_route_id(String beat_route_id) {
            this.beat_route_id = beat_route_id;
        }

        public String getRt_code() {
            return rt_code;
        }

        public void setRt_code(String rt_code) {
            this.rt_code = rt_code;
        }

        public String getNd_name() {
            return nd_name;
        }

        public void setNd_name(String nd_name) {
            this.nd_name = nd_name;
        }

        public String getRd_name() {
            return rd_name;
        }

        public void setRd_name(String rd_name) {
            this.rd_name = rd_name;
        }

        public String getSp_name() {
            return sp_name;
        }

        public void setSp_name(String sp_name) {
            this.sp_name = sp_name;
        }

        public String getSp_id() {
            return sp_id;
        }

        public void setSp_id(String sp_id) {
            this.sp_id = sp_id;
        }
    }
}
