package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by root on 24/10/16.
 */
public class StateBean {


    private int count;
    private String status;
    public ArrayList<DataState> data;

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

    public ArrayList<DataState> getData() {
        return data;
    }

    public void setData(ArrayList<DataState> data) {
        this.data = data;
    }

    public class DataState {
        private int state_id;
        private String state_name;

        public int getState_id() {
            return state_id;
        }

        public void setState_id(int state_id) {
            this.state_id = state_id;
        }

        public String getState_name() {
            return state_name;
        }

        public void setState_name(String state_name) {
            this.state_name = state_name;
        }
    }
}
