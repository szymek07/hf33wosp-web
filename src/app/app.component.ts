import {Component, OnInit} from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { ApiResponse } from './response.model';
import { ScheduleResponse } from './schedule.model';
import { ActivityResponse } from './activity.model';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, CommonModule, FormsModule, HttpClientModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit {
  loadingLastHrd: boolean = false;
  loadingSchedule: boolean = false;
  loadingAward: boolean = false;
  scheduleResponse: ScheduleResponse[] = [];
  activityResponse: ActivityResponse[] = [];

  hours: string[] = Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0'));

  call: string = '';
  submitted: boolean = false;
  apiResponse: ApiResponse | undefined;

  loadingImage: boolean = true;


  getSchedule() {
    const url = `/api/schedule`;
    this.loadingSchedule = true;
    this.http.get<ScheduleResponse[]>(url).subscribe(
      response => {
        this.scheduleResponse = response;
        this.loadingSchedule = false;
        console.log('Schedule response:', this.scheduleResponse);
      },
      error => {
        this.loadingSchedule = false;
        console.error('API error:', error);
      }
    );
  }

  getActivity() {
    const url = `/api/last-heard?stationId=13&limit=10&diffInSec=1800`;
    this.loadingLastHrd = true;
    this.http.get<ActivityResponse[]>(url).subscribe(
      response => {
        this.activityResponse = response;
        this.loadingLastHrd = false;
        // console.log('Activity response:', this.activityResponse);
      },
      error => {
        this.loadingLastHrd = false;
        // console.error('API error:', error);
      }
    );
  }

  onSubmit(form: any) {
    // console.log('Form submitted: ', form.value);
    // console.log('Form valid:', form.valid);
    this.submitted = true;
    this.makeApiCall(this.call);
  }

  resetSubmitted(): void {
    console.log('Resetting Form');
    this.submitted = false;
  }

  makeApiCall(callValue: string) {
    const url = `/api/points?stationId=13&call=${callValue}`;
    this.loadingAward = true;
    this.http.get<ApiResponse>(url).subscribe(
      response => {
        this.apiResponse = response;
        this.loadingAward = false;
        console.log('API response:', response);
      },
      error => {
        this.loadingAward = false;
        // console.error('API error:', error);
      }
    );
  }

  convertToMHz(freq: string): number {
    const freqNumber = parseFloat(freq);
    return freqNumber / 1_000_000;
  }

  onImageLoad() {
    this.loadingImage = false;
  }

  constructor(private http: HttpClient) {}

  title = 'HF33WOSP';

  ngOnInit(): void {
    this.getActivity();
    this.getSchedule();
  }
}
