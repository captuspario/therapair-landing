import type { ApiOverviewResponse } from "./types";

export const sampleOverview: ApiOverviewResponse = {
  totals: [
    { eventType: "SENT", _count: 210 },
    { eventType: "DELIVERED", _count: 204 },
    { eventType: "OPENED", _count: 168 },
    { eventType: "CLICKED", _count: 94 },
    { eventType: "UNSUBSCRIBED", _count: 6 },
  ],
  funnel: [
    { stage: "Invited", count: 210 },
    { stage: "Clicked", count: 94 },
    { stage: "Started", count: 76 },
    { stage: "Completed", count: 61 },
  ],
  sentiment: [
    { sentiment: "POSITIVE", _count: 38 },
    { sentiment: "NEUTRAL", _count: 19 },
    { sentiment: "NEGATIVE", _count: 9 },
  ],
  daily: Array.from({ length: 14 }).map((_, index) => {
    const date = new Date();
    date.setDate(date.getDate() - (13 - index));
    const emailSent = 12 + Math.round(Math.random() * 6);
    const emailDelivered = emailSent - Math.round(Math.random() * 2);
    const emailOpened = Math.round(emailDelivered * (0.7 + Math.random() * 0.1));
    const emailClicked = Math.round(emailOpened * (0.45 + Math.random() * 0.1));
    const surveyStarted = Math.round(emailClicked * (0.8 + Math.random() * 0.1));
    const surveyCompleted = Math.round(surveyStarted * (0.78 + Math.random() * 0.05));

    return {
      date: date.toISOString(),
      emailSent,
      emailDelivered,
      emailOpened,
      emailClicked,
      surveyInvited: emailSent,
      surveyStarted,
      surveyCompleted,
      feedbackCount: Math.round(3 + Math.random() * 3),
      positiveCount: Math.round(2 + Math.random() * 2),
      neutralCount: Math.round(1 + Math.random()),
      negativeCount: Math.round(Math.random() * 1),
      campaign: {
        id: "campaign-wave-1",
        name: "Therapist Research Wave 1",
        type: "EMAIL_RESEARCH_WAVE",
      },
    };
  }),
  alerts: [
    {
      type: "info",
      message: "Wave 1 performance is steady. Monitor cohort split next week.",
    },
  ],
  feedbackHighlights: [
    {
      id: "f-1",
      text: "Appreciated the clarity on how feedback will be used. The tone was personable.",
      sentiment: "POSITIVE",
      category: "General feedback",
      themeCluster: "Outreach quality",
      source: "SURVEY",
      createdAt: new Date().toISOString(),
    },
    {
      id: "f-2",
      text: "The survey felt a little long—consider trimming duplicate questions.",
      sentiment: "NEUTRAL",
      category: "Survey length",
      themeCluster: "Survey improvements",
      source: "SURVEY",
      createdAt: new Date().toISOString(),
    },
    {
      id: "f-3",
      text: "One call-to-action link didn’t work; please double-check the Resend template.",
      sentiment: "NEGATIVE",
      category: "Broken links",
      themeCluster: "Operational issues",
      source: "EMAIL_REPLY",
      createdAt: new Date().toISOString(),
    },
  ],
};

